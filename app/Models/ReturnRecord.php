<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnRecord extends Model
{
    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function med_package()
    {
        return $this->belongsTo(MedPackage::class);
    }
}
