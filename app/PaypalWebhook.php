<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaypalWebhook extends Model
{
    protected $fillable = [
        'type',
        'paypal_data',
    ];

    /**Start Relations */
    /**End Relations */

    /**Start Scopes*/
    /**End Scopes */

    /**Start Mutators*/
    public function setPaypalDataAttribute($value)
    {
        $this->attributes['paypal_data'] = json_encode($value);
    }
    /**End Mutators */

    /**Start Helper*/
    public function getTypesWebhooks()
    {
        return [
            'PAYMENT.SALE.COMPLETED',
            'PAYMENT.SALE.REFUNDED',
            'PAYMENT.SALE.REVERSED',
            'BILLING.SUBSCRIPTION.CREATED',
            'BILLING.SUBSCRIPTION.RENEWED',
            'BILLING.SUBSCRIPTION.ACTIVATED',
            'BILLING.SUBSCRIPTION.UPDATED',
            'BILLING.SUBSCRIPTION.EXPIRED',
            'BILLING.SUBSCRIPTION.CANCELLED',
            'BILLING.SUBSCRIPTION.SUSPENDED',
        ];
    }
    /**End Helper*/
}
