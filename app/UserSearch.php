<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSearch extends Model
{
    protected $fillable = [
        'name',
        'data',
    ];

    protected $casts = [
        'data' => 'json',
    ];
    /**Start Relations */
    /**End Relations */

    /**Start Scopes*/
    /**End Scopes */

    /**Start Mutators*/
    public function setDataAttribute($value)
    {
        $this->attributes['data'] = json_encode($value);
    }
    /**End Mutators */

    /**Start Helper*/
    /**End Helper*/
}
