<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\AuthService;
use App\Http\Requests\Admin\Auth\Login as AuthLoginRequest;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(AuthLoginRequest $request)
    {
        return $this->authService->login($request);
    }

    public function user(Request $request)
    {
        return $this->authService->user($request);
    }
}
