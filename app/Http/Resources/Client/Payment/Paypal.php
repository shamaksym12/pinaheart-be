<?php

namespace App\Http\Resources\Client\Payment;

use Illuminate\Http\Resources\Json\JsonResource;

class Paypal extends JsonResource
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
            'type' => 'paypal',
            'plan' => $this->plan,
            'status' => strtolower($this->status),
            'auto_renewal' => (bool) $this->auto_renewal,
            'start' => optional($this->start_time)->toDateTimeString(),
            'next' => optional($this->next_billing_time)->toDateTimeString(),
            'payments' => PaypalPayment::collection($this->whenLoaded('payments')),
        ];
    }
}
