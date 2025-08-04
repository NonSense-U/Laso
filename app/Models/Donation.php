<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function medPackage()
    {
        return $this->belongsTo(MedPackage::class);
    }
}
