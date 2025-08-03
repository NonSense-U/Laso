<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pharmacy extends Model
{
    /** @use HasFactory<\Database\Factories\PharmacyFactory> */
    use HasFactory;

    public function staff()
    {
        return $this->hasMany(User::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function med_packages()
    {
        return $this->hasMany(MedPackage::class);
    }

    public function fast_selling_items()
    {
        return $this->hasMany(FastSellingItem::class);
    }

    public function vaults()
    {
        return $this->hasMany(Vault::class);
    }

    public function whitelist()
    {
        return $this->hasMany(PharmacyStaffWhitelist::class);
    }

}
