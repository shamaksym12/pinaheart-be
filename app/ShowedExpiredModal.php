<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShowedExpiredModal extends Model
{
    protected $fillable = ['user_id', 'subscription_type', 'subscription_id'];
}
