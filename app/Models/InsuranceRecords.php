<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsuranceRecords extends Model
{
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function insurance()
    {
        $this->belongsTo(Insurance::class);
    }
}
