<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hash extends Model
{
    const TYPE_RECOVERY = 'recovery_password';

    protected $fillable = [
        'hash',
        'type',
        'expired_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleted(function ($model)
        {
            $model->users()->detach();
        });
    }

    /**Start Relations */
    public function users()
    {
        return $this->morphedByMany(User::class, 'hashable')->withTimestamps();
    }
    /**End Relations */

    /**Start Scopes*/
    /**End Scopes */

    /**Start Mutators*/
    /**End Mutators */

    /**Start Helper*/
    /**End Helper*/
}
