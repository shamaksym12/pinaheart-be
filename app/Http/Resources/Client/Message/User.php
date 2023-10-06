<?php

namespace App\Http\Resources\Client\Message;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
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
            'last_name' => $this->last_name,
            'age' => $this->age,
            'sex' => $this->sex,
            'online' => $this->last_activity_at >= now()->subMinutes(3),
            'main_photo' => $this->whenLoaded('mainPhoto', function(){
                return storageUrl(optional($this->mainPhoto)->path);
            }),
            'main_photo_thumb' => $this->whenLoaded('mainPhoto', function(){
                return storageUrl(optional($this->mainPhoto)->path_thumb);
            }),
            'formatted_address' => $this->whenLoaded('location', function(){
                return optional($this->location)->full_address;
            }),
            'looking_for' => $this->whenLoaded('match', function(){
                return optional($this->match)->looking_for;
            }),
            'is_favorite' => $this->whenLoaded('favoritedByUsers', function(){
                return (bool) $this->favoritedByUsers->count();
            }),
            'is_interested' => $this->whenLoaded('interestedByUsers', function(){
                return (bool) $this->interestedByUsers->count();
            }),
            'is_blocked' => $this->whenLoaded('blockedByUsers', function(){
                return (bool) $this->blockedByUsers->count();
            }),
            'is_admin_block' => $this->is_admin_block,
        ];
    }
}
