<?php

namespace App\Http\Resources\Admin\Coupon;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CouponPaginateCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->resource;
    }
}
