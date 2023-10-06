<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserOff extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'reason',
        'off_at',
        'on_at',
    ];

    protected $dates = [
        'off_at',
        'on_at',
    ];

    /**Start Relations */
    /**End Relations */

    /**Start Scopes*/
    /**End Scopes */

    /**Start Mutators*/
    /**End Mutators */

    /**Start Helper*/
    /**End Helper*/
}
