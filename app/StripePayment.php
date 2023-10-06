<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StripePayment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'plan_id',
        'subcription_id',
        'stripe_invoice_id',
        'stripe_invoice_status',
        'stripe_payment_intent_id',
        'stripe_payment_intent_status',
        'amount',
        'stripe_data',
    ];
    /**Start Relations */
    public function plan() {
        return $this->hasOne(Plan::class, 'id', 'plan_id');
    }
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
    /**End Helper*/
}
