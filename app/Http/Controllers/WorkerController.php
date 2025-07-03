<?php

namespace App\Http\Controllers;

use App\Http\Requests\Sales\CheckoutRequest;
use App\Services\SalesService;
use App\Services\WorkerService;
use App\Traits\V1\ApiResponse;
use Illuminate\Http\Request;

class WorkerController extends Controller
{
    use ApiResponse;

    private $workerService, $salesService;

    public function __construct(WorkerService $workerService, SalesService $salesService)
    {
        $this->workerService = $workerService;
        $this->salesService = $salesService;
    }


    public function checkout(CheckoutRequest $request)
    {
        $data = $this->salesService->Checkout($request->validated(), $request->user());
        return ApiResponse::success('Sale was successfull.', $data);
    }
}
