<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\MedPackage;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Support\Facades\DB;
use Throwable;

class DonationsService
{

    public function donateMeds(array $payload)
    {
        DB::beginTransaction();
        try {
            $donations = collect($payload['donations']);
            $medPackages = MedPackage::whereIn('id', $donations->pluck('id'))->get()->keyBy('id');

            foreach ($donations as $donation) {
                $medPackage = $medPackages->get($donation['id']);

                if (!$medPackage) {
                    throw new \Exception("MedPackage with ID {$donation['id']} not found.");
                }

                if ($medPackage->quantity < $donation['quantity']) {
                    throw new \Exception("Insufficient stock.");
                }

                $medPackage->quantity -= $donation['quantity'];
                $medPackage->save();

                Donation::create([
                    'pharmacy_id' => $medPackage->pharmacy_id,
                    'med_package_id' => $medPackage->id,
                    'quantity' => $donation['quantity']
                ]);
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function addPublicDonation($amount, User $user)
    {
        $vault = Vault::where('pharmacy_id', $user->pharmacy_id)
            ->where('name', 'charity')
            ->first();

        if (!$vault) {
            throw new \Exception("Charity vault not found for pharmacy.");
        }

        $vault->balance += $amount;
        $vault->save();
    }
}
