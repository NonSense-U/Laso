<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    /** @use HasFactory<\Database\Factories\PatientFactory> */
    use HasFactory;

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function debts()
    {
        return $this->hasMany(Debt::class);
    }

    public function insurance()
    {
        return $this->hasOne(Insurance::class);
    }

    public function insurance_records()
    {
        return $this->hasMany(InsuranceRecords::class); 
    }
}
