<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    const ADMIN_PAGINATE_PER_PAGE = 20;

    const STATUS_ACTIVE = 'active';
    const STATUS_PAUSED = 'paused';
    const STATUS_DELETED = 'deleted';

    protected $fillable = [
        'status',
        'code',
        'count_days',
        'max_uses',
        'expired_at',
    ];

    protected $dates = [
        'expired_at',
        'created_at',
        'updated_at',
    ];

    /**Start Relations */
    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps()->withPivot(['started_at', 'expired_at']);
    }
    /**End Relations */

    /**Start Scopes*/
    public function scopeCustomOrder($query, $field, array $values, $type = 'DESC')
    {
        foreach($values as $key => $value) {
            $values[$key] = "'".$value."'";
        }
        $orderTypes = implode(',',$values);
        return $query->orderByRaw("FIELD(".$field.",".$orderTypes.") ".$type);
    }
    /**End Scopes */

    /**Start Mutators*/
    /**End Mutators */

    /**Start Helper*/
    public function isActive()
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    public function isPaused()
    {
        return $this->status == self::STATUS_PAUSED;
    }

    public function isDeleted()
    {
        return $this->status == self::STATUS_DELETED;
    }

    public function isExpired(Carbon $date = null)
    {
        $date = $date ?? now();
        return now()->gt($this->expired_at);
    }

    public function isFull(int $currentCount = null)
    {
        $currentCount = $currentCount ?? $this->loadCount('users')->users_count;
        return $currentCount >= $this->max_uses;
    }
    /**End Helper*/
}
