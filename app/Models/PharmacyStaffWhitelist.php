<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PharmacyStaffWhitelist extends Model
{
    protected $table = 'pharmacy_staff_whitelist';
    
    public function pharmacy()
    {
        $this->belongsTo(Pharmacy::class);
    }
}
