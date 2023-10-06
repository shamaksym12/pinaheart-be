<?php

namespace App\Http\Resources\Client\Payment;

use Illuminate\Http\Resources\Json\JsonResource;

class StripePayment extends JsonResource
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
            'status' => $this->stripe_payment_intent_status,
            'amount' => $this->amount,
            'currency' => optional($this->stripe_data)->currency,
            'date' => ($date = optional($this->stripe_data)->date) ? \Carbon\Carbon::createFromTimestamp($date)->toDateTimeString() : null,
        ];
    }
}
