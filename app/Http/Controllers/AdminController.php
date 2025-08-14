<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Services\AdminService;
use App\Traits\V1\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function drawExpenses(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:salary,maintenance,daily,other'],
            'amount' => ['required', 'decimal:0,2', 'min:0'],
            'note' => ['sometimes','string']
        ]);
        $this->adminService->drawExpenses($validated, $request->user());
        return ApiResponse::success('moeny drawn successfully.');
    }


    public function getExpenses(Request $request)
    {
        $user = $request->user();
        $expenses = $this->adminService->getExpenses($user);
        return ApiResponse::success('ok', ['expenses' => $expenses]);
    }
}
