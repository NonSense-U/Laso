<?php

namespace App\Helpers;

use App\Models\Medication;
use Carbon\Exceptions\UnreachableException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

class medicationPriceHelper
{

    public static function getPrices()
    {
        $response = Http::get('http://127.0.0.1:5000/medications'); // include http://
        if (!$response->successful()) {
            throw new UnreachableException();
        }

        $medications = $response->json();
        $redisData = [];

        foreach ($medications as $medication) {
            // Insert or update in DB
            Medication::updateOrCreate(
                ['serial_number' => $medication['serial_number']], // unique key
                [
                    'name'            => $medication['name'],
                    'scientific_name' => $medication['scientific_name'],
                    'strength'        => $medication['strength'],
                    'entities'        => $medication['entities'],
                    'retail_price'    => $medication['retail_price'],
                    'notes'           => $medication['notes'],
                    'manufacturer_id' => $medication['manufacturer_id'],
                ]
            );

            // Also prepare Redis data
            $redisData[$medication['serial_number'] . '_medication_price'] = $medication['retail_price'];
        }

        // Better to set all Redis keys at once
        if (!empty($redisData)) {
            Redis::mset($redisData);
        }
    }
}
