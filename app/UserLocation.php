<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserLocation extends Model
{
    protected $fillable = [
        'country_id',
        'formatted_address',
        'place_id',
        'lat',
        'long',
    ];
    /**Start Relations */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    /**End Relations */

    /**Start Scopes*/
    public function scopeWithDistanceKm($query, $lat, $long)
    {
        $distanseKm = '(SELECT *, (((acos(sin(('.$lat.'*pi()/180)) *
            sin((lat*pi()/180))+cos(('.$lat.'*pi()/180)) *
            cos((lat*pi()/180)) * cos((('.$long.'-
            `long`)*pi()/180))))*180/pi())*60*1.1515*1.609344)
        as distance_km
        FROM user_locations )';
        return $query->fromRaw($distanseKm.' user_locations');
    }
    /**End Scopes */

    /**Start Mutators*/
    public function getFullAddressAttribute()
    {
        return $this->formatted_address ? $this->formatted_address : optional($this->country)->name;
    }
    /**End Mutators */

    /**Start Helper*/
    /**End Helper*/
}
