<?php

namespace App\Http\Resources\Client\Message;

use Illuminate\Http\Resources\Json\JsonResource;

class Message extends JsonResource
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
            'is_paid' => $this->is_paid,
            'dialog_id' => $this->dialog_id,
            'text' => $this->is_paid ? $this->text : $this->text_masked,
            'my' => (bool) $this->my,
            'is_read' => (bool) $this->is_read,
            'created_at' => $this->getDateFormat($this->created_at),
            'date_utc' => $this->created_at
        ];
    }

    public function getDateFormat($date) {
        return $date->isToday() ? $date->format('H:i') : $date->diffForHumans();
    }
}
