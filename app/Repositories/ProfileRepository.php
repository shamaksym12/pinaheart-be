<?php

namespace App\Repositories;

use App\Param;
use App\User;
use App\ParamValue;

class ProfileRepository
{
    public function getAllProfileParamsWithValue()
    {
        return Param::where('is_profile', true)->with('values')->get();
    }

    public function getAllMatchParams()
    {
        return Param::where('is_matches', true)->get();
    }

    public function getAllMatchParamsWithValue()
    {
        return Param::where('is_matches', true)->with('values')->get();
    }

    public function getAllSearchParams()
    {
        return Param::where('is_search', true)->get();
    }

    public function getAllSearchParamsWithValue()
    {
        return Param::where('is_search', true)->with('values')->get();
    }

    public function findParamByAlias(string $alias)
    {
        return Param::where('alias', strtolower($alias))->first();
    }

    public function getParamsByAliases(array $aliases)
    {
        return Param::whereIn('alias', $aliases)->get();
    }

    public function findParamValueByName(Param $param, string $name)
    {
        return ParamValue::where('param_id', $param->id)->where('name', strtolower($name))->first();
    }

    public function getAllParams()
    {
        return Param::get();
    }

    public function loadShortProfile(User $user, User $me)
    {
        return $user->load(['location.country', 'match',
        'photos' => function($q){
            $q->approved();
        },
        'profileParams' => function($q){
            $q->shortProfileParams();
        },
        'favoritedByUsers' => function($q) use($me){
            $q->where('who_id', $me->id);
        },
        'interestedByUsers' => function($q) use($me){
            $q->where('who_id', $me->id);
        },
        'blockedByUsers' => function($q) use($me){
            $q->where('who_id', $me->id);
        },
        'coupons' => function($q){
            $q->where('coupon_user.expired_at', '>', now());
        },
        ]);
    }

    public function loadDetailProfile(User $user, User $me)
    {
        return $user->load(['location.country', 'match', 'info', 'profileParams.values', 'interest', 'personality',
        'photos' => function($q){
            $q->approved();
        },
        'favoritedByUsers' => function($q) use($me){
            $q->where('who_id', $me->id);
        },
        'interestedByUsers' => function($q) use($me){
            $q->where('who_id', $me->id);
        },
        'blockedByUsers' => function($q) use($me){
            $q->where('who_id', $me->id);
        },
        'coupons' => function($q){
            $q->where('coupon_user.expired_at', '>', now());
        },
        ]);
    }
}
