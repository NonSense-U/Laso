<?php

namespace App\Http\Controllers;

use App\Services\AdminService;
use App\Traits\V1\ApiResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    use ApiResponse;

    private $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }


    public function getWorkers(Request $request)
    {
        $workers = $this->adminService->getWorkers($request->user());
        return ApiResponse::success('ok', $workers);
    }


    public function enableWorker(Request $request, $worker_id)
    {
        $this->adminService->enableWorker($request->user(), $worker_id);
        return ApiResponse::success('Worker enabled successfully.');
    }

    public function disableWorker(Request $request, $worker_id)
    {
        $this->adminService->disableWorker($request->user(), $worker_id);
        return ApiResponse::success('Worker disabled successfully.');
    }
}
