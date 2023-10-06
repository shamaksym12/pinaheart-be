<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StripeSubscriber extends Model
{
    protected $fillable = [
        'user_id',
        'stripe_id',
        'stripe_data',
    ];

    /**Start Relations */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    /**End Relations */

    /**Start Scopes*/
    /**End Scopes */

    /**Start Mutators*/
    public function setStripeDataAttribute($value)
    {
        $this->attributes['stripe_data'] = json_encode($value);
    }
    /**End Mutators */

    /**Start Helper*/
    /**End Helper*/
}
