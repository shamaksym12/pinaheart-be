<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StripeWebhook extends Model
{
    protected $fillable = [
        'type',
        'stripe_data',
    ];

    /**Start Relations */
    /**End Relations */

    /**Start Scopes*/
    /**End Scopes */

    /**Start Mutators*/
    public function getStripeDataAttribute($value)
    {
        return json_decode($value);
    }

    public function setStripeDataAttribute($value)
    {
        $this->attributes['stripe_data'] = json_encode($value);
    }
    /**End Mutators */

    /**Start Helper*/
    public function getTypesWebhooks()
    {
        return [
            'customer.subscription.updated',
            'customer.subscription.deleted',
            'invoice.payment_failed',
            'invoice.payment_succeeded',
        ];
    }
    /**End Helper*/
}
