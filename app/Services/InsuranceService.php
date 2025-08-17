<?php

namespace App\Services;

use App\Models\Insurance;
use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class InsuranceService
{
    public function addInsurance(array $payload, User $user)
    {
        $payload['user_id'] = $user->id;
        $payload['pharmacy_id'] = $user->pharmacy_id;
        $insurance = Insurance::create($payload);
        return $insurance;
    }

    public function getInsuranceRecords(Pharmacy $pharmacy)
    {
        $records = DB::select(
            'SELECT p.full_name, i.provider, ir.cart_id as cart, ir.discounted_amount, ir.status FROM patients p
            JOIN insurances i ON p.id = i.patient_id
            JOIN insurance_records ir ON i.id = ir.insurance_id
            WHERE ir.status = \'pending\'
            ',
            []
        );
        return $records;
    }
}
