<?php

namespace App\Services;

use App\Models\MedPackage;
use App\Models\PackagesOrder;
use App\Models\ReturnRecord;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class MedPackageService
{
    public function getExpiredMeds(User $user)
    {
        $packages = $user->pharmacy
            ->med_packages()
            ->where('quantity', '>', 0)
            ->where('expiration_date', '>', now())
            ->join('packages_orders', 'med_packages.packages_order_id', '=', 'packages_orders.id')
            ->join('suppliers', 'packages_orders.supplier_id', '=', 'suppliers.id')
            ->select('med_packages.*', 'suppliers.name as supplier_name')
            ->with('medication') // still eager load medication normally
            ->get();

        $grouped = $packages->groupBy(
            function ($package) {
                return $package->medication_id;
            }
        );

        $medications = $grouped->map(function ($groupedPackages) use (&$info) {
            $medication = $groupedPackages->first()->medication;
           
            $groupedPackages->each(function ($package) use (&$info) {
                unset($package->medication);
            });

            return [
                'serial_number' => $medication->serial_number,
                'name' => $medication->name,
                'scientific_name' => $medication->scientific_name,
                'indications' => $medication->indications,
                'side_effects' => $medication->side_effects,
                'price' => $medication->retail_price,
                'strength' => $medication->strength,
                'entities' => $medication->entities,
                'stock' => $groupedPackages->sum('quantity'), //! sum of package quantities
                'expires_in' => $groupedPackages->min('expiration_date'),
                'packages' => $groupedPackages,
            ];
        })->values();

        return $medications;
    }


    public function getMedsLogs(array $payload, User $user)
    {
        // $fromDate = match ($payload['scope']) {
        //     'today' => Carbon::today(),
        //     'lastWeek' => Carbon::now()->subWeek(),
        //     'lastMonth' => Carbon::now()->subMonth(),
        // };

        $fromDate = now()->subMonths(6);

        $initQuantity = DB::select(
            'SELECT m.name AS medication_name, SUM(mp.init_quantity) AS init_quantity 
         FROM med_packages mp
         JOIN medications m ON mp.medication_id = m.id 
         WHERE mp.pharmacy_id = ? AND mp.created_at > ? 
         GROUP BY m.name',
            [$user->pharmacy_id, $fromDate]
        );

        $soldMeds = DB::select(
            'SELECT m.name AS medication_name, SUM(ci.quantity) AS sold_amount 
         FROM cart_items ci 
         JOIN med_packages mp ON ci.product_id = mp.id 
         JOIN medications m ON mp.medication_id = m.id
         WHERE mp.pharmacy_id = ? AND ci.type = ? AND mp.created_at > ? 
         GROUP BY m.name',
            [$user->pharmacy_id, 'med_package', $fromDate]
        );

        $donatedMeds = DB::select(
            'SELECT m.name AS medication_name, SUM(dn.quantity) AS donated_amount 
         FROM donations dn 
         JOIN med_packages mp ON dn.med_package_id = mp.id 
         JOIN medications m ON mp.medication_id = m.id
         WHERE dn.pharmacy_id = ? AND mp.created_at > ? 
         GROUP BY m.name',
            [$user->pharmacy_id, $fromDate]
        );

        $returnedMeds = DB::select(
            'SELECT m.name AS medication_name, SUM(rr.returned_quantity) AS returned_amount 
         FROM return_records rr 
         JOIN med_packages mp ON rr.med_package_id = mp.id 
         JOIN medications m ON mp.medication_id = m.id
         WHERE rr.pharmacy_id = ? AND mp.created_at > ? 
         GROUP BY m.name',
            [$user->pharmacy_id, $fromDate]
        );

        $currentMeds = DB::select(
            'SELECT m.name AS medication_name, SUM(mp.quantity) AS current_amount 
         FROM med_packages mp 
         JOIN medications m ON mp.medication_id = m.id
         WHERE mp.pharmacy_id = ? AND mp.expiration_date > ? AND mp.created_at > ? 
         GROUP BY m.name',
            [$user->pharmacy_id, now()->addMonth(), $fromDate]
        );

        $expiredMeds = DB::select(
            'SELECT m.name AS medication_name, SUM(mp.quantity) AS expired_amount 
         FROM med_packages mp 
         JOIN medications m ON mp.medication_id = m.id
         WHERE mp.pharmacy_id = ? AND mp.created_at > ? AND mp.expiration_date < ? 
         GROUP BY m.name',
            [$user->pharmacy_id, $fromDate, now()]
        );


        $mergedData = [];

        $processDataset = function ($dataset, $valueKey) use (&$mergedData) {
            foreach ($dataset as $item) {
                $medName = $item->medication_name;

                if (!isset($mergedData[$medName])) {
                    $mergedData[$medName] = [
                        'medication_name' => $medName,
                        'init_quantity'   => 0,
                        'sold_amount'     => 0,
                        'donated_amount'  => 0,
                        'returned_amount' => 0,
                        'current_amount'  => 0,
                        'expired_amount'  => 0,
                    ];
                }

                $mergedData[$medName][$valueKey] = $item->{$valueKey};
            }
        };

        $processDataset($initQuantity, 'init_quantity');
        $processDataset($soldMeds, 'sold_amount');
        $processDataset($donatedMeds, 'donated_amount');
        $processDataset($returnedMeds, 'returned_amount');
        $processDataset($currentMeds, 'current_amount');
        $processDataset($expiredMeds, 'expired_amount');

        return array_values($mergedData);
    }

    public function getStorage(User $user)
    {
        // dd(now()->toDateString());
        $packages = $user->pharmacy
            ->med_packages()
            ->where('quantity', '>', 0)
            ->join('packages_orders', 'med_packages.packages_order_id', '=', 'packages_orders.id')
            ->join('suppliers', 'packages_orders.supplier_id', '=', 'suppliers.id')
            ->select('med_packages.*', 'suppliers.name as supplier_name')
            ->with('medication') // still eager load medication normally
            ->get();

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
                'serial_number' => $medication->serial_number,
                'name' => $medication->name,
                'scientific_name' => $medication->scientific_name,
                'indications' => $medication->indications,
                'side_effects' => $medication->side_effects,
                'price' => $medication->retail_price,
                'strength' => $medication->strength,
                'entities' => $medication->entities,
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
                'paid_amount' => $payload['paid_amount']
            ]);

            $medPackages = [];

            foreach ($payload['packages_order'] as $package) {
                $package['packages_order_id'] = $packages_order->id;
                $package['pharmacy_id'] = $user->pharmacy_id;
                $package['init_quantity'] = $package['quantity'];
                $medPackages[] = MedPackage::create($package);
            }

            //! Check For Debt
            $debt = $payload['total_price'] - $payload['paid_amount'];
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

    public function returnMeds(array $payload, User $user)
    {
        try {
            DB::beginTransaction();

            $medPackage = MedPackage::findOrFail($payload['med_package_id']);

            if ($payload['returned_quantity'] <= 0) {
                throw new \Exception('Returned quantity must be greater than zero.');
            }

            if ($medPackage->quantity < $payload['returned_quantity']) {
                throw new \Exception('Insufficient stock to return.');
            }

            $payload['pharmacy_id'] = $user->pharmacy_id;

            $returnRecord = ReturnRecord::create($payload);

            //! decrease stock
            $medPackage->quantity -= $payload['returned_quantity'];
            $medPackage->save();

            $cashValue = $medPackage->purchase_price * $payload['returned_quantity'];

            $parentOrder = $medPackage->parent_order;

            if (!$parentOrder) {
                throw new \Exception('No parent order associated with this medication package.');
            }

            $debt = $parentOrder->total_price - $parentOrder->paid_amount;
            $parentOrder->total_price -= $cashValue;

            if ($debt > 0) {
                if ($debt >= $cashValue) {
                    $cashValue = 0;
                } else {
                    $cashValue -= $debt;
                    $parentOrder->paid_amount = $parentOrder->total_price;
                }
                $parentOrder->save();
            }

            $mainVault = $user->pharmacy->vaults()->where('name', 'main')->first();

            if (!$mainVault) {
                throw new \Exception('Main vault not found.');
            }

            $mainVault->balance += $cashValue;
            $mainVault->save();

            DB::commit();

            return ['return_record' => $returnRecord];
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
