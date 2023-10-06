<?php
namespace App\Collections;

use Illuminate\Database\Eloquent\Collection;
use App\Param;

class ParamCollection extends Collection
{
    public function getProfileParamValue(Param $param)
    {
        $exists = $this->where('id', $param->id);
        if($exists->count()) {
            switch ($param->type) {
                case Param::TYPE_FIXED:
                    return $exists->first()->pivot->value;
                    break;
                case Param::TYPE_ONE:
                    $first =$exists->first();
                    $valueId = $first->pivot->value_id;
                    return optional($first->values->firstWhere('id', $valueId))->name;
                    break;
                case Param::TYPE_MANY:
                    $first = $exists->first();
                    $valueIds = $exists->pluck('pivot.value_id');
                    return $first->values->whereIn('id', $valueIds)->pluck('name');
                    break;
            }
        } else {
            return null;
        }
    }
}