<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedPackage extends Model
{
    /** @use HasFactory<\Database\Factories\MedPackageFactory> */
    use HasFactory;

    public function medication()
    {
        return $this->belongsTo(Medication::class);
    }

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
