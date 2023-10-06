<?php

namespace App\Http\Resources\Client\Payment;

use Illuminate\Http\Resources\Json\JsonResource;

class PaypalPayment extends JsonResource
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
            'amount' => $this->amount,
            'currency' => optional(optional($this->paypal_data)->amount)->currency,
            'date' => optional(toCarbon(optional($this->paypal_data)->create_time))->toDateTimeString(),
        ];
    }
}
