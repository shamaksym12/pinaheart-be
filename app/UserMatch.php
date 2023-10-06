<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserMatch extends Model
{
    protected $fillable = [
        'sex',
        'age_from',
        'age_to',
        'country_id',
        'formatted_address',
        'place_id',
        'lat',
        'long',
        'distance',
        'distance_unit',
    ];
    /**Start Relations */
    /**End Relations */

    /**Start Scopes*/
    /**End Scopes */

    /**Start Mutators*/
    public function getSexNameAttribute()
    {
        switch ($this->sex) {
            case 'M':
                return 'Male';
                break;
            case 'F':
                return 'Female';
                break;
            default:
                return 'Male or Female';
                break;
        }
    }

    public function getLookingForAttribute()
    {
        return $this->sex_name.' '.$this->age_from.($this->age_to ? '-'.$this->age_to : '');
    }
    /**End Mutators */

    /**Start Helper*/
    /**End Helper*/
}
