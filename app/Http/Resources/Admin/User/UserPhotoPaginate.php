<?php

namespace App\Http\Resources\Admin\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserPhotoPaginate extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'sex' => $this->sex,
            'status' => $this->status,
            'email' => $this->email,
            'age' => $this->age,
            'created_at' => $this->created_at->toDateTimeString(),
            'photos' => Photo::collection($this->whenLoaded('photos')),
            'profile_id' => $this->profile_id,
        ];
    }
}
