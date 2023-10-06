<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    const TYPE_VIEW = 'view';
    const TYPE_ADD_TO_FAVORITE = 'add_to_favorite';
    const TYPE_REMOVE_FROM_FAVORITE = 'remove_from_favorite';
    const TYPE_ADD_TO_INTEREST = 'add_to_interest';
    const TYPE_REMOVE_FROM_INTEREST = 'remove_from_interest';
    const TYPE_ADD_TO_BLOCK = 'add_to_block';
    const TYPE_REMOVE_FROM_BLOCK = 'remove_from_block';

    const TYPES = [self::TYPE_VIEW];

    protected $fillable = [
        'who_id',
        'whom_id',
        'type',
    ];

    /**Start Relations */
    /**End Relations */

    /**Start Scopes*/
    public function scopeUser($query, User $user)
    {
        return $query->where(function($q) use($user){
            $q->where('who_id', $user->id);
            $q->orWhere('whom_id', $user->id);
        });
    }
    /**End Scopes */

    /**Start Mutators*/
    /**End Mutators */

    /**Start Helper*/
    /**End Helper*/
}
