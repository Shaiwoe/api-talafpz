<?php

namespace App\Http\Controllers\Order;

use App\Models\Order;
use App\Models\Course;
use App\Models\OrderItems;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use App\Http\Resources\Order\OrderResource;

class OrderController extends ApiController
{
    public function index()
    {
        $orders = Order::paginate(10);

        return $this->successResponse([
            'orders' => OrderResource::collection($orders->load('orderItem')),
            'links' => OrderResource::collection($orders)->response()->getData()->links,
            'meta' => OrderResource::collection($orders)->response()->getData()->meta,
        ]);
    }

    public static function create($request, $coupone, $amounts, $token)
    {
        DB::beginTransaction();

        $order = Order::create([
            'user_id' => Auth()->id(),
            'total_amount' => $amounts['totalAmount'],
            'coupon_amount' => $amounts['couponAmount'],
            'paying_amount' => $amounts['payingAmount'],
            'coupon_id' => $coupone == null ? null : $coupone->id
        ]);

        foreach ($request->cart as $OrderItems) {
            $course = Course::findOrFail($OrderItems['id']);
            OrderItems::create([
                'order_id' => $order->id,
                'course_id' => $course->id,
                'price' => $course->is_sale ? $course->sale_price : $course->price,
                'quantity' => $OrderItems['qty'],
                'subtotal' => ($course->price * $OrderItems['qty'])
            ]);
        }

        Transaction::create([
            'user_id' => Auth()->id(),
            'order_id' => $order->id,
            'amount' => $amounts['payingAmount'],
            'token' => $token
        ]);

        DB::commit();
    }

    public static function update($token, $transId)
    {
        DB::beginTransaction();

        $transaction = Transaction::where('token', $token)->firstOrFail();

        $transaction->update([
            'status' => 1,
            'trans_id' => $transId
        ]);

        $order = Order::findOrFail($transaction->order_id);

        $order->update([
            'status' => 1,
            'payment_status' => 1
        ]);

        foreach (OrderItems::where('order_id', $order->id)->get() as $item) {
            $course = Course::find($item->course_id);
            $course->update([
                'quantity' => ($course->quantity -  $item->quantity)
            ]);
        }

        DB::commit();
    }
}
