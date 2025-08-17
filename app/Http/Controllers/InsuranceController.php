<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInsuranceRequest;
use App\Services\InsuranceService;
use App\Traits\V1\ApiResponse;
use Illuminate\Http\Request;

class InsuranceController extends Controller
{
    use ApiResponse;
    private $insuranceService;

    public function __construct(InsuranceService $insuranceService)
    {
        $this->insuranceService = $insuranceService;
    }

    public function addInsurance(StoreInsuranceRequest $request)
    {
        $insurance = $this->insuranceService->addInsurance($request->validated(), $request->user());
        return ApiResponse::success('ok', ['insurance' => $insurance], 201);
    }

    public function getInsuranceRecords(Request $request)
    {
        $records = $this->insuranceService->getInsuranceRecords($request->user()->pharmacy);
        return ApiResponse::success('ok', ['records' => $records], 200);
    }
}
