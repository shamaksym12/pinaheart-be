<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AuthPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function manageFront(User $user)
    {
        return $user->isActive();
    }

    public function manageAdmin(User $user)
    {
        $is_allow_access = $user->isAdmin() || $user->isManager() || $user->isJunior();
        return $user->isActive() && $is_allow_access;
    }
}
