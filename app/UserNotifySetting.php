<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserNotifySetting extends Model
{
    const TYPE_EMAIL = 'email';

    const NAME_NEW_MESSAGE = 'new-message';
    const NAME_NEW_ACTIVITY = 'new-activity';

    const VALUE_DAILY = 'daily';
    const VALUE_WEEKLY = 'weekly';
    const VALUE_NEVER = 'never';

    protected $fillable = [
        'type',
        'name',
        'value',
    ];

    /**Start Relations */
    /**End Relations */

    /**Start Scopes*/
    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }
    /**End Scopes */

    /**Start Mutators*/
    /**End Mutators */

    /**Start Helper*/
    public function getTypes()
    {
        return [self::TYPE_EMAIL];
    }

    public function getNames()
    {
        return [self::NAME_NEW_MESSAGE, self::NAME_NEW_ACTIVITY];
    }

    public function getValues()
    {
        return [self::VALUE_DAILY, self::VALUE_WEEKLY, self::VALUE_NEVER];
    }
    /**End Helper*/
}
