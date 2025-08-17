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

    public function customerReturnMeds(Request $request)
    {
        $validated = $request->validate(
            [
                'medication_id' => ['required', 'exists:medications,id'],
                'expiration_date' => ['required','date'],
                'return_quantity' => ['required', 'integer', 'min:0']
            ]
        );

        $this->salesService->customerReturnMeds($validated, $request->user());
        
        return ApiResponse::success('medications returned successfully.');
    }
}
