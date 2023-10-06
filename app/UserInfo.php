<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    protected $fillable = [
        'heading',
        'about',
        'looking',
    ];

    /**Start Relations */
    /**End Relations */

    /**Start Scopes*/
    /**End Scopes */

    /**Start Mutators*/
    /**End Mutators */

    /**Start Helper*/
    public function isFilled()
    {
        return ! empty($this->heading) && ! empty($this->about) && ! empty($this->looking);
    }
    /**End Helper*/
}
