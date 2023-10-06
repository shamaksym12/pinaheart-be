<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaypalSubscription extends Model
{
    protected $fillable = [
        'plan_id',
        'status',
        'auto_renewal',
        'paypal_id',
        'paypal_order_id',
        'start_time',
        'next_billing_time',
        'paypal_data',
    ];

    protected $dates = [
        'start_time',
        'next_billing_time',
    ];

    /**Start Relations */
    public function plan() {
        return $this->hasOne(Plan::class, 'id', 'plan_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(PaypalPayment::class, 'subscription_id');
    }
    /**End Relations */

    /**Start Scopes*/
    public function setPaypalDataAttribute($value)
    {
        $this->attributes['paypal_data'] = json_encode($value);
    }
    /**End Scopes */

    /**Start Mutators*/
    /**End Mutators */

    /**Start Helper*/
    /**End Helper*/
}
