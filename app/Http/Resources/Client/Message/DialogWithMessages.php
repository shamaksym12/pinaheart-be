<?php

namespace App\Http\Resources\Client\Message;

use Illuminate\Http\Resources\Json\JsonResource;

class DialogWithMessages extends JsonResource
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
            'has_paid_user' => $this->has_paid_user,
            'is_deleted' => $this->is_deleted,
            'user' => new User($this->whenLoaded('user')),
            'messages' => Message::collection($this->whenLoaded('messages')),
        ];
    }
}
