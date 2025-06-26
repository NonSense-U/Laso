<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateAdminRequest;
use App\Http\Requests\CreateWorkerRequest;
use App\Services\Account\UserService;
use App\Traits\V1\ApiResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponse;

    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function createAdmin(CreateAdminRequest $request)
    {
        $data = $this->userService->createAdmin($request->validated());
        return ApiResponse::success('Admin created successfully.', $data, 201);
    }

    public function createWorker(CreateWorkerRequest $request)
    {
        $data = $this->userService->createWorker($request->validated());
        return ApiResponse::success('Worker created successfully.', $data, 201);
    }
}
