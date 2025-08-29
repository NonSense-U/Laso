<?php

namespace App\Services;

use App\Helpers\vaultsHelper;
use App\Models\PackagesOrder;
use App\Models\Supplier;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Throwable;

class SupplierService
{

    public function createSupplier(array $payload, User $user)
    {
        $payload['pharmacy_id'] = $user->pharmacy_id;
        $supplier = Supplier::create($payload);
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

    public function getRecord($supplier_id)
    {
        $supplier = Supplier::findOrFail($supplier_id);
        $records = $supplier->packages_orders()->with('packages')->get();
        return $records;
    }


    public function paySupplierDebt($order_id, $amount, $user)
    {
        try {
            DB::beginTransaction();

            $record = PackagesOrder::findOrFail($order_id);
            $vault = vaultsHelper::getMain($user);

            $record->paid_amount += $amount;
            $vault->balance -= $amount;

            $record->save();
            $vault->save();

            DB::commit();
            return $record;

        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
