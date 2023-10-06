<?php

namespace App\Http\Resources\Admin\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserPaginate extends JsonResource
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
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'sex' => $this->sex,
            'profile_id' => $this->profile_id,
            'status' => $this->status,
            'account_status' => $this->account_status, //mutator
            'admin_comment' => $this->whenLoaded('adminData', function(){
                return optional($this->adminData)->comment;
            }),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
