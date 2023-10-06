<?php

namespace App\Http\Resources\Client\User;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CustomData;

class Login extends JsonResource
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
            'role' => $this->role,
            'email' => $this->email,
            'status' => $this->status,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'sex' => $this->sex,
            'age' => $this->age,
            'is_soc_user' => $this->is_soc_user,
            'is_off' => $this->is_off,
            'photos_count' => $this->photos_count,
            //custom
            'tokens' => $this->when($this->tokens, function(){
                return $this->tokens;
            }),
            'is_admin_block' => $this->is_admin_block,
        ];
    }
}
