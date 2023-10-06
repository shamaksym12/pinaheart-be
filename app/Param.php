<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Collections\ParamCollection;

class Param extends Model
{
    const TYPE_ONE = 'one';
    const TYPE_MANY = 'many';
    const TYPE_FIXED = 'fixed';

    protected $fillable = [
        'is_profile',
        'is_matches',
        'is_search',
        'type',
        'type_match',
        'type_search',
        'name',
        'alias',
        'data',
    ];

    public function newCollection(array $models = [])
    {
        return new ParamCollection($models);
    }

    /**Start Relations */
    public function values()
    {
        return $this->hasMany(ParamValue::class);
    }
    /**End Relations */

    /**Start Scopes*/
    public function scopeProfile($query)
    {
        return $query->where('is_profile', true);
    }

    public function scopeShortProfileParams($query)
    {
        return $query->whereIn('alias', ['height','weight']);
    }
    /**End Scopes */

    /**Start Mutators*/
    public function setDataAttribute($value)
    {
        $this->attributes['data'] = json_encode($value);
    }

    public function getDataAttribute($value)
    {
        return json_decode($value);
    }
    /**End Mutators */

    /**Start Helper*/
    /**End Helper*/
}
