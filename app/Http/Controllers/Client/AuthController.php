<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Client\AuthService;
use App\Http\Requests\Client\Auth\Login as AuthLoginRequest;
use App\Http\Requests\Client\Auth\Register as AuthRegisterRequest;
use App\Http\Requests\Client\Auth\Recovery as AuthRecoveryRequest;
use App\Http\Requests\Client\Auth\Reset as AuthResetRequest;
use App\Http\Requests\Client\Auth\Redirect as AuthRedirectRequest;

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

    public function loginByHash(Request $request)
    {
        return $this->authService->loginByHash($request);
    }

    public function register(AuthRegisterRequest $request)
    {
        return $this->authService->register($request);
    }

    public function recovery(AuthRecoveryRequest $request)
    {
        return $this->authService->recovery($request);
    }

    public function reset(AuthResetRequest $request)
    {
        return $this->authService->reset($request);
    }

    public function redirect(AuthRedirectRequest $request)
    {
        return $this->authService->redirect($request);
    }

    public function google(Request $request)
    {
        return $this->authService->google($request);
    }

    public function facebook(Request $request)
    {
        return $this->authService->facebook($request);
    }
    
    public function unblock(Request $request)
    {
        return $this->authService->unblock($request);
    }
}
