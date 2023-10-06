<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\DB;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function getProfile(User $user, User $model)
    {
        return $model->isActive() && ! $model->isAdmin();
    }

    public function getDetailProfile(User $user, User $model)
    {
        return ($this->getProfile($user, $model) && ! DB::table('user_blocked_users')->where('whom_id', $user->id)->where('who_id', $model->id)->exists());
    }
}
