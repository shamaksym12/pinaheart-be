<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StripeSubscription extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'plan_id',
        'stripe_id',
        'status',
        'cancel_at_period_end',
        'period_start',
        'period_end',
        'stripe_data',
    ];

    protected $dates = [
        'period_start',
        'period_end',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model)
        {
        });
    }
    /**Start Relations */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(StripePayment::class, 'subcription_id');
    }

    public function plan() {
        return $this->hasOne(Plan::class, 'id', 'plan_id');
    }

    /**End Relations */

    /**Start Scopes*/
    /**End Scopes */

    /**Start Mutators*/
    public function getHandStatusAttribute()
    {
        /* if($this->status == 'active') {
            return $this->cancel_at_period_end == true ? 'suspended' : 'active';
        } */
        return $this->status;
    }

    public function setStripeDataAttribute($value)
    {
        $this->attributes['stripe_data'] = json_encode($value);
    }
    /**End Mutators */

    /**Start Helper*/
    /**End Helper*/
}
