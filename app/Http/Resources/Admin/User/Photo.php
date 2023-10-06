<?php

namespace App\Http\Resources\Admin\User;

use Illuminate\Http\Resources\Json\JsonResource;

class Photo extends JsonResource
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
            'path' => storageUrl($this->path),
            'path_thumb' => storageUrl($this->path_thumb),
            'is_main' => $this->is_main,
            'approved' => $this->approved,
            'verified_at' => $this->verified_at,
        ];
    }
}
