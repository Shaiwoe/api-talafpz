<?php

namespace App\Http\Controllers\Coupone;

use App\Http\Controllers\ApiController;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Coupone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Coupone\CouponeResource;

class CouponeController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $coupone = Coupone::orderBy('created_at', 'desc')->paginate(5);

        return $this->successResponse([
            'coupone' => CouponeResource::collection($coupone),
            'links' => CouponeResource::collection($coupone)->response()->getData()->links,
            'meta' => CouponeResource::collection($coupone)->response()->getData()->meta,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|unique:coupones,code',
            'percentage' => 'required|integer',
            'expired_at' => 'required|date_format:Y-m-d H:i:s'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        DB::beginTransaction();

        $coupone = Coupone::create([
            'code' => $request->code,
            'percentage' => $request->percentage,
            'expired_at' => $request->expired_at
        ]);

        DB::commit();

        return $this->successResponse(new CouponeResource($coupone), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Coupone $coupone)
    {
        return $this->successResponse(new CouponeResource($coupone));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Coupone $coupone)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|unique:coupones,code,' . $coupone->id,
            'percentage' => 'required|integer',
            'expired_at' => 'nullable|date_format:Y-m-d H:i:s'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        DB::beginTransaction();

        $coupone->update([
            'code' => $request->code,
            'percentage' => $request->percentage,
            'expired_at' => $request->expired_at !== null ? $request->expired_at : $coupone->expired_at
        ]);

        DB::commit();

        return $this->successResponse(new CouponeResource($coupone), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupone $coupone)
    {
        DB::beginTransaction();
        $coupone->delete();
        DB::commit();

        return $this->successResponse(new CouponeResource($coupone), 200);
    }

    public function check(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        $coupone = Coupone::where('code', $request->code)->where('expired_at', '>', Carbon::now())->first();

        if ($coupone == null) {
            return $this->errorResponse(['error' => ['کد تخفیف وارد شده وجود ندارد']], 422);
        }

        if (Order::where('user_id', Auth()->id())->where('coupon_id', $coupone->id)->where('payment_status', 1)->exists()) {
            return $this->errorResponse(['error' => ['شما قبلا از این کد تخفیف استفاده کرده اید']], 422);
        }

        return $this->successResponse([
            'percentage' => $coupone->percentage
        ], 200);
    }
}
