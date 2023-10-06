<?php

namespace App\Http\Resources\Admin\Coupon;

use Illuminate\Http\Resources\Json\JsonResource;

class CouponPaginate extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'code' => $this->code,
            'count_days' => $this->count_days,
            'max_uses' => $this->max_uses,
            'users_count' => $this->users_count,
            'expired_at' => optional($this->expired_at)->toDateTimeString(),
            'is_expired' => $this->expired_at ? now()->gt($this->expired_at) : true,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
