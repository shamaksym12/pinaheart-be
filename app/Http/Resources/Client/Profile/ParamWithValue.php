<?php

namespace App\Http\Resources\Client\Profile;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CustomData;

class ParamWithValue extends JsonResource
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
            'alias' => $this->alias,
            'param_id' => $this->whenPivotLoaded('user_params', function(){
                return $this->pivot->param_id;
            }),
            'value_id' => $this->whenPivotLoaded('user_params', function(){
                return $this->pivot->value_id;
            }),
        ];
    }
}
