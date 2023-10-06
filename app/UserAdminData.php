<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAdminData extends Model
{
    protected $fillable = [
        'comment',
        'comented_at',
        'blocked_at',
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
