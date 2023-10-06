<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserParam extends Model
{
    protected $fillable = [

    ];
    /**Start Relations */
    public function value()
    {
        return $this->belongsTo(ParamValue::class, 'value_id');
    }
    /**End Relations */

    /**Start Scopes*/
    /**End Scopes */

    /**Start Mutators*/
    /**End Mutators */

    /**Start Helper*/
    /**End Helper*/
}
