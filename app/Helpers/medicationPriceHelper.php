<?php

namespace App\Helpers;

use App\Models\Medication;
use Carbon\Exceptions\UnreachableException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

class medicationPriceHelper
{
    public static function getPrices()
    {
        $response = Http::get('127.0.0.1:5000/medications');
        if(!$response->successful())
        {
            throw new UnreachableException();
        }
        $medications = $response->json();

        $redisData = [];

        foreach ($medications as $medication) {
            $redisData[$medication['serial_number'] . '_medication_price'] = $medication['retail_price'];
        }

        //! better to set all keys at once
        if (!empty($redisData)) {
             Redis::mset($redisData);
        }
    }
}
