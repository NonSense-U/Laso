<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function med_packages()
    {
        return $this->hasMany(MedPackage::class);
    }
}
