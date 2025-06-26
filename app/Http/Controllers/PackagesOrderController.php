<?php

namespace App\Http\Controllers;

use App\Models\PackagesOrder;
use App\Http\Requests\StorePackagesOrderRequest;
use App\Http\Requests\UpdatePackagesOrderRequest;
use App\Traits\V1\ApiResponse;
use Illuminate\Http\Request;

class PackagesOrderController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $packagesOrders = PackagesOrder::query()->where('pharmacy_id', '=', $request->user()->pharmacy_id)->get();
        return ApiResponse::success('Packages Orders have been retrieved successfully', $packagesOrders);
    }

    public function show(Request $request, $packages_order_id)
    {
        $packagesOrder = PackagesOrder::findOrFail($packages_order_id);
        return ApiResponse::success('Supplier retrieved succesfully.', ['packages' => $packagesOrder->packages]);
    }

    public function store(StorePackagesOrderRequest $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePackagesOrderRequest $request, PackagesOrder $packagesOrder)
    {
        //
    }
}
