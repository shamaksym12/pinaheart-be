<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserFavorite extends Model
{
    protected $fillable = [
        'who_id',
        'whom_id'
    ];
    /**Start Relations */
    /**End Relations */

    /**Start Scopes*/
    public function scopeFavorite($query, $who, $whom)
    {
        return $query->where('who_id', $who)->where('whom_id', $whom)->get();
    }
    /**End Scopes */

    /**Start Mutators*/
    /**End Mutators */

    /**Start Helper*/
    /**End Helper*/
}
