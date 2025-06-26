<?php

namespace App\Http\Controllers;

use App\Models\MedPackage;
use App\Http\Requests\StoreMedPackagesRequest;
use App\Http\Requests\UpdateMedPackageRequest;
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
        return ApiResponse::success('Med packages retrieved successfully.',$data,200);
    }

    public function addMedPackages(StoreMedPackagesRequest $request)
    {
        $data = $this->medPackagesServices->addMedPackages($request->validated(), $request->user());

        return ApiResponse::success('Medication Packages has been added successfully.',$data);
    }

}
