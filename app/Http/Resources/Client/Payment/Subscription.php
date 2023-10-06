<?php

namespace App\Http\Resources\Client\Payment;

use Illuminate\Http\Resources\Json\JsonResource;
use App\StripeSubscription;
use App\PaypalSubscription;

class Subscription extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        switch (true) {
            case $this->resource instanceof StripeSubscription:
                return new Stripe($this->resource);
                break;
            case $this->resource instanceof PaypalSubscription:
                return new Paypal($this->resource);
                break;
            }
    }
}
