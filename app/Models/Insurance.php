<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Insurance extends Model
{
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
