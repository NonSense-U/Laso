<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function packages_orders()
    {
        return $this->hasMany(PackagesOrder::class);
    }
}
