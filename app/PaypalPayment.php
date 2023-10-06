<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaypalPayment extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_id',
        'paypal_id',
        'status',
        'amount',
        'paypal_data',
    ];

    /**Start Relations */
    public function subscription() {
        return $this->hasOne(PaypalSubscription::class, 'id', 'subscription_id');
    }
    /**End Relations */

    /**Start Scopes*/
    /**End Scopes */

    /**Start Mutators*/
    public function getPaypalDataAttribute($value)
    {
        return json_decode($value);
    }

    public function setPaypalDataAttribute($value)
    {
        $this->attributes['paypal_data'] = json_encode($value);
    }
    /**End Mutators */

    /**Start Helper*/
    /**End Helper*/
}
