<?php

namespace App\Http\Resources\Client\Profile;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CustomData;

class UserMatchesWithParams extends JsonResource
{
    use CustomData;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'sex' => optional($this)->sex,
            'age_from' => optional($this)->age_from,
            'age_to' => optional($this)->age_to,
            'country_id' => optional($this)->country_id,
            'formatted_address' => optional($this)->formatted_address,
            'place_id' => optional($this)->place_id,
            'lat' => optional($this)->lat,
            'long' => optional($this)->long,
            'distance' => optional($this)->distance,
            'distance_unit' => optional($this)->distance_unit,
            //custom
            'match_params' => $this->when(optional($this)->matchParams, function(){
                return $this->matchParams;
            })
        ];
    }
}
