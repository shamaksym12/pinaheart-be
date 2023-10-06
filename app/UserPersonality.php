<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPersonality extends Model
{
    protected $fillable = [
        'desc',
        'travel',
        'weekend',
        'humor',
        'person',
        'dress',
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
