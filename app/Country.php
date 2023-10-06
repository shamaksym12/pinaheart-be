<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'code',
    ];

    /**Start Relations */
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
    /**End Helper*/
}
