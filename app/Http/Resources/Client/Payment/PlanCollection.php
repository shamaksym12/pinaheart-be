<?php

namespace App\Http\Resources\Client\Payment;

use App\Http\Resources\CustomData;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PlanCollection extends ResourceCollection
{
    use CustomData;
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public $collects = PlanListItem::class;


    public function toArray($request)
    {
        return [
            'plans' => parent::toArray($request),
            'paypal_client_id' => $this->paypalClientId,
            'stripe_api_key' => $this->stripeApiKey,
        ];
    }
}
