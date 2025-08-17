<?php

namespace App\Services;

use App\Models\Debt;
use App\Models\Insurance;
use App\Models\Patient;
use App\Models\User;

class PatientService
{
    public function storePatient(array $payload, User $user)
    {
        $payload['pharmacy_id'] = $user->pharmacy_id;
        $payload['admitted_by'] = $user->id;
        $patient = Patient::Create($payload);
        return $patient;
    }

    public static function addDebt($cart_id, $patient_id, $pharmacy_id)
    {
        $patient = Patient::findOrFail($patient_id);
        Debt::create([
            'pharmacy_id' => $pharmacy_id,
            'cart_id' => $cart_id,
            'patient_id' => $patient->id
        ]);
        $patient->load('debts');
        return $patient;
    }

    public function payDebt($debt_id)
    {
        $debt = Debt::findOrFail($debt_id);
        $debt->status = 'paid';
        $debt->save();
        return $debt;
    }

    public function deleteDebt($debt_id)
    {
        $debt = Debt::findOrFail($debt_id);
        $debt->delete();
        return;
    }
}