<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class SupplierService
{
    
    public function createSupplier(array $payload, User $user)
    {
        $payload['pharmacy_id'] = $user->pharmacy_id;
        $supplier = Supplier::create($payload);
        $supplier->load('pharmacy');
        return ['supplier' => $supplier];
    }


    public function updateSupplier(array $payload, User $user, $supplier_id)
    {
        $supplier = Supplier::findOrFail($supplier_id);

        if ($user->pharmacy_id !== $supplier->pharmacy_id) {
            throw new AccessDeniedHttpException();
        }

        $supplier->update($payload);
        return ['supplier' => $supplier];
    }

    public function deleteSupplier(User $user, $supplier_id)
    {
        $supplier = Supplier::findOrFail($supplier_id);
        if ($user->pharmacy->id !== $supplier->pharmacy_id) {
            throw new AccessDeniedHttpException();
        }
        $supplier->delete();
        return;
    }
}
