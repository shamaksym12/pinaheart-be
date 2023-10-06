<?php

namespace App\Http\Resources\Client\User;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CustomData;

class Short extends JsonResource
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
            'main_photo' => $this->whenLoaded('mainPhoto', function(){
                return storageUrl(optional($this->mainPhoto)->path);
            }),
            'main_photo_thumb' => $this->whenLoaded('mainPhoto', function(){
                return storageUrl(optional($this->mainPhoto)->path_thumb);
            }),
            'filled_info' => $this->whenLoaded('info', function(){
                return ! $this->info ? false : $this->info->isFilled();
            }),
            'has_default_match' => $this->whenLoaded('match', function(){
                return $this->hasDefaultMatch();
            }),
            'is_admin_block' => $this->is_admin_block,
            //custom
            $this->mergeWhen(($countOnlineMembers = $this->customGet('countOnlineMembers')), [
                'count_online_members' => $countOnlineMembers,
            ]),
        ];
    }
}
