<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShowedAutoRenewal extends Model
{
    protected $fillable = ['user_id', 'subscription_type', 'subscription_id'];
}
