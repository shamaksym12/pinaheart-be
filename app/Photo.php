<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    const USER_PHOTO_LIMIT = 9;

    protected $fillable = [
        'approved',
        'name',
        'path',
        'path_thumb',
        'verified_at',
        'is_main',
    ];

    protected $casts = [
        'approved' => 'boolean',
        'is_main' => 'boolean',
    ];

    /**Start Relations */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    /**End Relations */

    /**Start Scopes*/
    public function scopeApproved($query, $value = true)
    {
        return $query->where('approved', $value);
    }
    /**End Scopes */

    /**Start Mutators*/
    /**End Mutators */

    /**Start Helper*/
    public function beenApproved()
    {
        return ! is_null($this->approved);
    }

    public function isApproved()
    {
        return (bool)$this->approved;
    }

    public function isVerified()
    {
        return ! is_null($this->verified_at);
    }
    /**End Helper*/
}
