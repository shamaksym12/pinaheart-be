<?php

namespace App\Http\Resources\Admin\User;

use Illuminate\Http\Resources\Json\JsonResource;

class Coupon extends JsonResource
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
            'is_paid' => $this->is_paid,
            'email' => $this->email,
            'status' => $this->status,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'user_started_at' => $this->whenPivotLoaded('coupon_user', function(){
                return $this->pivot->started_at;
            }),
            'user_expired_at' => $this->whenPivotLoaded('coupon_user', function(){
                return $this->pivot->expired_at;
            }),
            'user_is_expired' => $this->whenPivotLoaded('coupon_user', function(){
                return ($expiredAt = $this->pivot->expired_at) ? now()->gt(toCarbon($expiredAt)) : true;
            }),
        ];
    }
}
