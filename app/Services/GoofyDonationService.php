<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\GoofyDonation;
use App\Models\Medication;
use App\Models\MedPackage;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

class GoofyDonationService
{
    public function donateMeds(array $payload, User $user)
    {
        DB::beginTransaction();
        try {
            $medication = Medication::query()
                ->where('name', '=', $payload['name'])
                ->where('strength', '=', $payload['strength'])
                ->firstOrFail();

            $required_quantity = $payload['quantity'];

            $packages = MedPackage::query()
                ->where('pharmacy_id', $user->pharmacy_id)
                ->where('medication_id', $medication->id)
                ->where('expiration_date', '>', now()->addMonths(3))
                ->where('quantity', '>', '0')
                ->orderBy('expiration_date', 'asc')
                ->lockForUpdate() // Prevent race conditions
                ->get();

            foreach ($packages as $package) {
                if ($required_quantity <= 0) break;

                $min = min($required_quantity, $package->quantity);

                MedPackage::where('id', $package->id)
                    ->update([
                        'quantity' => DB::raw("quantity - {$min}")
                    ]);

                $required_quantity -= $min;
            }

            if ($required_quantity > 0) {
                throw new \Exception("Not enough stock to donate {$payload['quantity']} units.");
            }

            GoofyDonation::create([
                'medication_name' => $medication->name,
                'pharmacy_id' => $user->pharmacy_id,
                'strength' => $payload['strength'],
                'quantity' => $payload['quantity'],
            ]);

            Expense::create([
                'pharmacy_id' => $user->pharmacy_id,
                'type' => 'donation',
                'amount_drawn' => $payload['quantity'],
                'note' => "Donated {$payload['quantity']} of {$medication->name} strength: {$payload['strength']}"
            ]);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
