<?php

namespace App\Http\Controllers;

use App\Http\Requests\Supplier\CreateSupplierRequest;
use App\Http\Requests\Supplier\UpdateSupplierRequest;
use App\Models\Supplier;
use App\Services\SupplierService;
use App\Traits\V1\ApiResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class SupplierController extends Controller
{
    use ApiResponse;

    private $supplierService;

    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
    }


    public function index(Request $request)
    {
        $suppliers = Supplier::query()->where('pharmacy_id', '=', $request->user()->pharmacy_id)->get();
        return ApiResponse::success('Suppliers retrieved succesfully.', $suppliers);
    }

    public function show(Request $request, $supplier_id)
    {
        $supplier = Supplier::findOrFail($supplier_id);
        $supplier->load('packages_orders');
        if ($request->user()->pharmacy_id !== $supplier->pharmacy_id) {
            throw new AccessDeniedHttpException();
        }
        return ApiResponse::success('Supplier retrieved succesfully.', ['supplier' => $supplier]);
    }

    public function createSupplier(CreateSupplierRequest $request)
    {
        $data = $this->supplierService->createSupplier($request->validated(), $request->user());
        return ApiResponse::success('Supplier added succesfully', $data, 201);
    }

    public function updateSupplier(UpdateSupplierRequest $request, $supplier_id)
    {
        $data = $this->supplierService->updateSupplier($request->validated(), $request->user(), $supplier_id);
        return ApiResponse::success('Supplier updated succesfully', $data, 200);
    }

    // public function deleteSupplier(Request $request, $supplier_id)
    // {
    //     $this->supplierService->deleteSupplier($request->user(), $supplier_id);
    //     return ApiResponse::success('Supplier deleted successfully.', [], 204);
    // }


    public function getSupplierRecord(Request $request, $supplier_id)
    {
        $data = $this->supplierService->getRecord($supplier_id);
        return ApiResponse::success('Supplier record retrieved successfully.', $data);
    }

    public function paySupplierDebt(Request $request, $order_id)
    {
        $validated = $request->validate([
            'amount' => ['required', 'decimal:0,2'],
        ]);

        $order = $this->supplierService->paySupplierDebt($order_id, $validated['amount'], $request->user());

        return ApiResponse::success('Debt has been paid successfully!', ['order' => $order]);
    }
}
