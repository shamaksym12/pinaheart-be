<?php

namespace App\Http\Controllers\Admin;

use App\Coupon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Admin\CouponService;
use App\Http\Requests\Admin\Coupon\Create as CouponCreateRequest;

class CouponController extends Controller
{
    protected $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    public function list(Request $request)
    {
        return $this->couponService->list($request);
    }

    public function create(CouponCreateRequest $request)
    {
        return $this->couponService->create($request);
    }

    public function pauseCoupon(Coupon $coupon, Request $request)
    {
        return $this->couponService->pauseCoupon($coupon, $request);
    }

    public function unpauseCoupon(Coupon $coupon, Request $request)
    {
        return $this->couponService->unpauseCoupon($coupon, $request);
    }

    public function deleteCoupon(Coupon $coupon, Request $request)
    {
        return $this->couponService->deleteCoupon($coupon, $request);
    }

    public function get(Coupon $coupon, Request $request)
    {
        return $this->couponService->get($coupon, $request);
    }

}
