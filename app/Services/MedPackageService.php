<?php

namespace App\Services;

use App\Models\MedPackage;
use App\Models\PackagesOrder;
use App\Models\Supplier;
use App\Models\User;
use Error;
use Exception;
use Illuminate\Support\Facades\DB;

class MedPackageService
{
    public function addMedPackages(array $payload, User $user)
    {
        DB::beginTransaction();

        try {
            $packages_order = PackagesOrder::create([
                'supplier_id' => $payload['supplier_id'],
                'pharmacy_id' => $user->pharmacy_id,
                'total_price' => $payload['total_price'],
                'paid_ammount' => $payload['paid_ammount']
            ]);

            $medPackages = [];

            foreach ($payload['packages-order'] as $package) {
                $package['packages_order_id'] = $packages_order->id;
                $package['pharmacy_id'] = $user->pharmacy_id;
                $medPackages[] = MedPackage::create($package);
            }

            //! Check For Debt
            $debt = $payload['total_price'] - $payload['paid_ammount'];
            if ($debt !== 0) {
                $supplier = Supplier::findOrFail($payload['supplier_id']);
                $supplier->balance += $debt;
                $supplier->save();
            }

            DB::commit();
            return $medPackages;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
