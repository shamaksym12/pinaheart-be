<?php

namespace App\Http\Resources\Client\User;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CustomData;

class Main extends JsonResource
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
            'email' => $this->email,
            'role' => $this->role,
            'status' => $this->status,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'sex' => $this->sex,
            'age' => $this->age,
            'is_soc_user' => $this->is_soc_user,
            'is_off' => $this->is_off,
            'is_busy' => $this->is_busy,
            'is_hidden' => $this->is_hidden,
            'is_paid' => $this->is_paid,
            'account_status' => $this->account_status, //mutator
            'free_platinum_until' => $this->free_platinum_until, //mutator
            'subscribe' => $this->subscribe,
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
            'inbox_unread_messages_count' => $this->inbox_unread_messages_count,
            'is_admin_block' => $this->is_admin_block,
            //custom
            $this->mergeWhen(($countOnlineMembers = $this->customGet('countOnlineMembers')), [
                'count_online_members' => $countOnlineMembers,
            ]),
            
        ];
    }
}
