<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\ApiController;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Course;
use App\Models\Coupone;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Order\OrderController;

class PaymentController extends ApiController
{
    public function send(Request $request)
    {
        // // return $request;
        // $validator = Validator::make($request->all(), [
        //     'cart' => 'required',
        //     'cart.*.id' => 'required|integer',
        //     'cart.*.qty' => 'required|integer',
        //     'coupon' => 'nullable|string',
        // ]);

        // if ($validator->fails()) {
        //     return $this->errorResponse($validator->messages(), 422);
        // }


        // $totalAmount = 0;
        // foreach ($request->cart as $orderItem) {
        //     $course = Course::findOrFail($orderItem['id']);
        //     if ($course->quantity < $orderItem['qty']) {
        //         return $this->errorResponse(['error' => ['تعداد محصول وارد شده اشتباه است']], 422);
        //     }

        //     $totalAmount += $course->is_sale ? $course->sale_price * $orderItem['qty'] : $course->price * $orderItem['qty'];
        // }

        // $couponAmount = 0;
        // $coupone = null;

        // if ($request->coupone) {
        //     $coupone = Coupone::where('code', $request->coupone)->where('expired_at', '>', Carbon::now())->first();

        //     if ($coupone == null) {
        //         return $this->errorResponse(['error' => ['کد تخفیف وارد شده وجود ندارد']], 422);
        //     }

        //     if (Order::where('user_id', Auth()->id())->where('coupon_id', $coupone->id)->where('payment_status', 1)->exists()) {
        //         return $this->errorResponse(['error' => ['شما قبلا از این کد تخفیف استفاده کرده اید']], 422);
        //     }

        //     $couponAmount = ($totalAmount * $coupone->percentage) / 100;
        // }

        // $payingAmount = $totalAmount - $couponAmount;

        // $amounts = [
        //     'totalAmount' => $totalAmount,
        //     'couponAmount' => $couponAmount,
        //     'payingAmount' => $payingAmount,
        // ];

        // return $amounts;

        $api = env('PAY_IR_API_KEY');
        // $amount = $payingAmount . '0';
        $amount = '100000';
        $mobile = "شماره موبایل";
        $factorNumber = "شماره فاکتور";
        $description = "توضیحات";
        $redirect = env('PAY_IR_CALLBACK_URL');
        $result = $this->sendRequest($api, $amount, $redirect, $mobile, $factorNumber, $description);
        $result = json_decode($result);
        if ($result->status) {
            // OrderController::create($request, $coupone, $amounts, $result->token);
            $go = "https://pay.ir/pg/$result->token";
            return $this->successResponse([
                'url' => $go
            ]);
        } else {
            return $this->errorResponse($result->errorMessage, 422);
        }
    }

    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'status' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        $api = env('PAY_IR_API_KEY');
        $token = $request->token;
        $result = json_decode($this->verifyRequest($api, $token));

        if (isset($result->status)) {
            if ($result->status == 1) {
                if (Transaction::where('trans_id', $result->transId)->exists()) {
                    return $this->successResponse([
                        'status' => false,
                        'error' => 'این تراکنش قبلا توی سیستم ثبت شده است'
                    ], 200);
                }
                OrderController::update($token, $result->transId);
                return $this->successResponse([
                    'status' => true,
                    'transId' => $result->transId
                ], 200);
            } else {
                return $this->errorResponse(['error' => ['تراکنش با خطا مواجه شد']], 422);
            }
        } else {
            if ($request->status == 0) {
                return $this->successResponse([
                    'status' => false,
                    'error' => 'تراکنش شما ناموفق بود'
                ], 200);
            }
        }
    }

    public function sendRequest($api, $amount, $redirect, $mobile = null, $factorNumber = null, $description = null)
    {
        return $this->curl_post('https://pay.ir/pg/send', [
            'api'          => $api,
            'amount'       => $amount,
            'redirect'     => $redirect,
            'mobile'       => $mobile,
            'factorNumber' => $factorNumber,
            'description'  => $description,
        ]);
    }

    function verifyRequest($api, $token)
    {
        return $this->curl_post('https://pay.ir/pg/verify', [
            'api'     => $api,
            'token' => $token,
        ]);
    }

    public function curl_post($url, $params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        $res = curl_exec($ch);
        curl_close($ch);

        return $res;
    }
}
