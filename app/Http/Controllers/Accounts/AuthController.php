<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\Account\AuthService;
use App\Traits\V1\ApiResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponse;

    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request)
    {
        $data = $this->authService->login($request->validated());
        return ApiResponse::success('Logged in successfully.', $data, 201);
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request);
        return ApiResponse::success('Logged out successfully.', [], 204);
    }
}
