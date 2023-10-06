<?php

namespace App\Http\Resources\Client\Profile;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CustomData;

class UserWithParams extends JsonResource
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
            'id' => $this->id,
            'profile_id' => $this->profile_id,
            'first_name' => $this->first_name,
            'sex' => $this->sex,
            'dob_day' => optional($this->dob)->format('j'),
            'dob_month' => optional($this->dob)->format('n'),
            'dob_year' => optional($this->dob)->format('Y'),
            'info' => new Info($this->whenLoaded('info')),
            'location' => new Location($this->whenLoaded('location')),
            //custom
            'profile_params' => $this->when($this->profileParams, function(){
                return $this->profileParams;
            })
        ];
    }
}
