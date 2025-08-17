<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    /** @use HasFactory<\Database\Factories\MedicationFactory> */
    use HasFactory;

    public function indications()
    {
        return $this->belongsToMany(Indication::class,'medications_indications','medication_id', 'indication_id');
    }

    public function side_effects()
    {
        return $this->belongsToMany(SideEffect::class, 'medications_side_effects', 'medication_id', 'side_effect_id');
    }
}
