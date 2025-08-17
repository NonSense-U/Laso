<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReturnMedsRequest;
use App\Http\Requests\StoreMedPackagesRequest;
use App\Services\MedPackageService;
use App\Traits\V1\ApiResponse;
use Illuminate\Http\Request;

class MedPackageController extends Controller
{
    use ApiResponse;

    private $medPackagesServices;

    public function __construct(MedPackageService $medPackageService)
    {
        $this->medPackagesServices = $medPackageService;
    }

    public function index(Request $request)
    {
        $data = $request->user()->pharmacy->med_packages;
        return ApiResponse::success('Med packages retrieved successfully.', $data, 200);
    }

    public function MedsLogs(Request $request)
    {
        $validated = $request->validate([
            'scope' => ['sometimes','string','in:today,lastWeek,lastMonth']
        ]);
        
        $data = $this->medPackagesServices->getMedsLogs($validated, $request->user());

        return ApiResponse::success('ok', $data);
    }

    public function addMedPackages(StoreMedPackagesRequest $request)
    {
        $data = $this->medPackagesServices->addMedPackages($request->validated(), $request->user());
// 
        return ApiResponse::success('Medication Packages has been added successfully.', $data);
    }

    public function returnMeds(ReturnMedsRequest $request)
    {
        $data = $this->medPackagesServices->returnMeds($request->validated(), $request->user());
        return ApiResponse::success('ok', $data);
    }


    public function showStorage(Request $request)
    {
        $data = $this->medPackagesServices->getStorage($request->user());
        return ApiResponse::success('Storage info retrieved successfully.', $data);
    }

    public function expiredMeds(Request $request)
    {
        $data = $this->medPackagesServices->getExpiredMeds($request->user());
        return ApiResponse::success('Expired meds retrieved successfully.', $data);
    }
}
