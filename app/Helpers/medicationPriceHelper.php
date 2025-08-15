<?php

namespace App\Helpers;

use App\Models\Medication;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class medicationPriceHelper
{
    public static function getPrices()
    {
        $medications = DB::select(
            'SELECT id, retail_price FROM medications'
        );

        $redisData = [];

        foreach ($medications as $medication) {
            $redisData[$medication->id . '_medication_price'] = $medication->retail_price;
        }

        //! better to set all keys at once
        if (!empty($redisData)) {
            Redis::mset($redisData);
        }
    }
}
