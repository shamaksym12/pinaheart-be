<?php

namespace App\Http\Resources\Client\Person;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Client\User\Photo as UserPhotoResourse;
use App\Http\Resources\CustomData;
use Illuminate\Support\Facades\DB;

class DetailProfile extends JsonResource
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
        

$timeFirst  = strtotime($this->last_activity_at);
$timeSecond = strtotime(now());
$diff = $timeSecond - $timeFirst;
        return [
            'id' => $this->id,
            'profile_id' => $this->profile_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'age' => $this->age,
            'sex' => $this->sex,
            'is_busy' => $this->is_busy,
            'account_status' => $this->account_status, //mutator
            'activity_diff_in_seconds' => $diff,
            'photos' => UserPhotoResourse::collection($this->whenLoaded('photos')),
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
            'comment' => $this->comment,
            $this->mergeWhen($this->relationLoaded('info'), new Info($this->info)),
            $this->mergeWhen($this->relationLoaded('interest'), new Interest($this->interest)),
            $this->mergeWhen($this->relationLoaded('personality'), new Personality($this->personality)),
            //custom
            $this->mergeWhen($this->handleProfileParams,  function(){
                return $this->handleProfileParams;
            }),
        ];
    }
}
