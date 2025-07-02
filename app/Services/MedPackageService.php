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

    public function getExpiredMeds(User $user)
    {
        $expiredMeds = $user->pharmacy->med_packages()->where('expiration_date', '<=', now()->toDateString())->with('medication')->get();
        return $expiredMeds;
    }

    public function getStorage(User $user)
    {
        // dd(now()->toDateString());
        $packages = $user->pharmacy->med_packages()->where('is_viable', '=', true)->with('medication')->get();

        $grouped = $packages->groupBy(
            function ($package) {
                return $package->medication_id;
            }
        );


        $info = [
            'total_meds' => 0,
            'expires_soon' => 0,
            'expired' => 0,
            'low_stock' => 0,
        ];

        $medications = $grouped->map(function ($groupedPackages) use (&$info) {
            $medication = $groupedPackages->first()->medication;

            $groupedPackages->each(function ($package) use (&$info) {
                unset($package->medication);

                if ($package->expiration_date <= now()->toDateString()) {
                    $info['expired'] += 1;
                } else if ($package->expiration_date <= now()->addWeeks(2)->toDateString()) {
                    $info['expires_soon'] += 1;
                }

                if ($package->quantity < 10) {
                    $info['low_stock'] += 1;
                }
            });

            return [
                'name' => $medication->name,
                'price' => $medication->price,
                'strength' => $medication->strength,
                'stock' => $groupedPackages->sum('quantity'), //! sum of package quantities
                'expires_in' => $groupedPackages->min('expiration_date'),
                'packages' => $groupedPackages,
            ];
        })->values();

        $info['total_meds'] = $medications->count();
        return ['overview' => $info, 'medications' => $medications];
    }

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
