<?php

namespace App\Http\Resources\Admin\User;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CustomData;

class DetailProfile extends JsonResource
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
            'email' => $this->email,
            'is_admin_block' => $this->is_admin_block,
            'admin_comment' => $this->comment,
            'sex' => $this->sex,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'age' => $this->age,
            'dob' => optional($this->dob)->toDateString(),
            'status' => $this->status,
            'account_status' => $this->account_status, //mutator
            // 'admin_comment' => $this->whenLoaded('adminData', function(){
            //     return optional($this->adminData)->comment;
            // }),
            'photos' => Photo::collection($this->whenLoaded('photos')),
            'formatted_address' => $this->whenLoaded('location', function(){
                return optional($this->location)->formatted_address;
            }),
            'looking_for' => $this->whenLoaded('match', function(){
                return optional($this->match)->looking_for;
            }),
            $this->mergeWhen($this->relationLoaded('info'), new Info($this->info)),
            $this->mergeWhen($this->relationLoaded('interest'), new Interest($this->interest)),
            $this->mergeWhen($this->relationLoaded('personality'), new Personality($this->personality)),
            //custom
            $this->mergeWhen($this->handleProfileParams,  function(){
                return $this->handleProfileParams;
            }),
        ];
    }
}
