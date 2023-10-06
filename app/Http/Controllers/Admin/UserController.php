<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\UserService;
use App\Http\Requests\Admin\User\SetComment as SetCommentRequest;
use App\User;
use App\Photo;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function list(Request $request)
    {
        return $this->userService->list($request);
    }

    public function updateEmail(User $user, Request $request)
    {
        return $this->userService->updateEmail($user, $request);
    }

    public function updatePassword(User $user, Request $request)
    {
        return $this->userService->updatePassword($user, $request);
    }

    public function get(User $user, Request $request)
    {
        return $this->userService->get($user, $request);
    }

    public function toggleUserBlock(User $user, Request $request)
    {
        return $this->userService->toggleUserBlock($user, $request);
    }

    public function setComment(User $user, SetCommentRequest $request)
    {
        return $this->userService->setComment($user, $request);
    }

    /**Start photos */
    public function listWithPhotos(Request $request)
    {
        return $this->userService->listWithPhotos($request);
    }

    public function approvePhoto(Photo $photo, Request $request)
    {
        return $this->userService->approvePhoto($photo, $request);
    }

    public function disapprovePhoto(Photo $photo, Request $request)
    {
        return $this->userService->disapprovePhoto($photo, $request);
    }
    /**End photos */

}
