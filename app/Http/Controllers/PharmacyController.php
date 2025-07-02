<?php

namespace App\Http\Controllers;

use App\Models\Pharmacy;
use App\Http\Requests\StorePharmacyRequest;
use App\Http\Requests\UpdatePharmacyRequest;
use App\Traits\V1\ApiResponse;
use Illuminate\Http\Request;

class PharmacyController extends Controller
{

    use ApiResponse;


    public function index()
    {
        $pharmacies = Pharmacy::all();

        return ApiResponse::success('Pharmacies retrieved successfully',$pharmacies);
    }

    public function store(StorePharmacyRequest $request)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pharmacy $pharmacy)
    {
        //
    }
}
