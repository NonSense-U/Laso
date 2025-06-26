<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackagesOrder extends Model
{
    /** @use HasFactory<\Database\Factories\PackagesOrderFactory> */
    use HasFactory;


    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function packages()
    {
        return $this->hasMany(MedPackage::class);
    }
}
