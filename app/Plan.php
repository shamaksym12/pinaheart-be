<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    const TYPE_PAYPAL = 'paypal';
    const TYPE_STRIPE = 'stripe';

    const UNIT_MONTH = 'month';

    protected $fillable = [
        'type',
        'origin_id',
        'name',
        'unit',
        'unit_count',
        'price',
    ];
    /**Start Relations */
    /**End Relations */

    /**Start Scopes*/
    /**End Scopes */

    /**Start Mutators*/
    /**End Mutators */

    /**Start Helper*/
    public function isStripe()
    {
        return $this->type == self::TYPE_STRIPE;
    }

    public function isPaypall()
    {
        return $this->type == self::TYPE_PAYPAL;
    }
    /**End Helper*/
}
