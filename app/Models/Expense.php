<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{

    public $timestamps = false;

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }
}
