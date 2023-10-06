<?php

namespace App\Repositories;

use App\Coupon;

class CouponRepository
{
    public function firstByCode(string $code)
    {
        return Coupon::where('code', strtolower($code))->first();
    }

    public function create(array $data)
    {
        return Coupon::create($data);
    }

    public function update(Coupon $coupon, array $data)
    {
        $coupon->fill($data)->save();
        return $coupon;
    }
}
