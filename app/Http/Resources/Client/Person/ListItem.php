<?php

namespace App\Http\Resources\Client\Person;
use App\Http\Resources\Client\User\Photo as UserPhotoResourse;
use Illuminate\Http\Resources\Json\JsonResource;
use App\UserInterestUser;
use App\UserFavorite;

class ListItem extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $interests = UserInterestUser::query();
        $interests = $interests->interest(auth()->user()->id, $this->id);

        $favorites = UserFavorite::query();
        $favorites = $favorites->favorite(auth()->user()->id, $this->id);

        return [
            'id' => $this->id,
            'profile_id' => $this->profile_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'sex' => $this->sex,
            'age' => $this->age,
            'account_status' => $this->account_status, //mutator
            'activity_diff_in_seconds' => $this->activity_diff_in_seconds,            
            'photos_count' => $this->photos_count,
            'is_busy' => $this->is_busy,
            'photos' => UserPhotoResourse::collection($this->photos),
            'main_photo' => $this->whenLoaded('mainPhoto', function(){
                return storageUrl(optional($this->mainPhoto)->path);
            }),
            'main_photo_thumb' => $this->whenLoaded('mainPhoto', function(){
                return storageUrl(optional($this->mainPhoto)->path_thumb);
            }),
            'formatted_address' => $this->whenLoaded('location', function(){
                return optional($this->location)->full_address;
            }),
            'looking_for' => $this->whenLoaded('match', function(){
                return optional($this->match)->looking_for;
            }),
            'is_blocked' => $this->whenLoaded('blockedByUsers', function(){
                return (bool) $this->blockedByUsers->count();
            }),
            'comment' => $this->comment,
            'is_admin_block' => $this->is_admin_block,
            'is_interested' => (bool) count($interests),
            'is_favorite' => (bool) count($favorites)
        ];
    }
}
