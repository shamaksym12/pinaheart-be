<?php

namespace App\Http\Resources\Client\Activity;

use Illuminate\Http\Resources\Json\JsonResource;

class Inbox extends JsonResource
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
            'sex' => $this->sex,
            'type' => $this->whenPivotLoaded('activities', function(){
                return $this->pivot->type;
            }),
            'first_name' => $this->first_name,
            'created_at' => $this->whenPivotLoaded('activities', function(){
                return $this->pivot->updated_at->diffForHumans();
            }),
            'main_photo' => $this->whenLoaded('mainPhoto', function(){
                return storageUrl(optional($this->mainPhoto)->path);
            }),
            'main_photo_thumb' => $this->whenLoaded('mainPhoto', function(){
                return storageUrl(optional($this->mainPhoto)->path_thumb);
            }),
        ];
    }
}
