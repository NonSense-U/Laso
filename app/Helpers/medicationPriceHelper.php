<?php

// namespace App\Helpers;

// use App\Models\Medication;
// use Carbon\Exceptions\UnreachableException;
// use Illuminate\Support\Facades\Http;
// use Illuminate\Support\Facades\Redis;

// class medicationPriceHelper
// {

//     public static function getPrices()
//     {
//         $response = Http::get(env('FASTAPI_URL') . '/medications'); // include http://
//         if (!$response->successful()) {
//             throw new UnreachableException();
//         }

//         $medications = $response->json();
//         $redisData = [];

//         foreach ($medications as $medication) {
//             // Insert or update in DB
//             Medication::updateOrCreate(
//                 ['serial_number' => $medication['serial_number']], // unique key
//                 [
//                     'name'            => $medication['name'],
//                     'scientific_name' => $medication['scientific_name'],
//                     'strength'        => $medication['strength'],
//                     'entities'        => $medication['entities'],
//                     'retail_price'    => $medication['retail_price'],
//                     'notes'           => $medication['notes'],
//                     'manufacturer_id' => $medication['manufacturer_id'],
//                 ]
//             );

//             // Also prepare Redis data
//             $redisData[$medication['serial_number'] . '_medication_price'] = $medication['retail_price'];
//         }

//         // Better to set all Redis keys at once
//         if (!empty($redisData)) {
//             Redis::mset($redisData);
//         }
//     }
// }



namespace App\Helpers;

use App\Models\Medication;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Carbon\Exceptions\UnreachableException;

class medicationPriceHelper
{
    public static function getPrices()
    {
        if (!Storage::exists('medications.json')) {
            throw new UnreachableException('medications.json not found in storage.');
        }
        
        $json = Storage::get('medications.json');

        $medications = json_decode($json, true);
        if (!is_array($medications)) {
            throw new UnreachableException('Invalid JSON format in medications.json.');
        }

        $redisData = [];

        foreach ($medications as $medication) {

            Medication::updateOrCreate(
                ['serial_number' => $medication['serial_number']],
                [
                    'name'            => $medication['name'] ?? null,
                    'scientific_name' => $medication['scientific_name'] ?? null,
                    'strength'        => $medication['strength'] ?? null,
                    'entities'        => $medication['entities'] ?? null,
                    'retail_price'    => $medication['retail_price'] ?? null,
                    'notes'           => $medication['notes'] ?? null,
                    'manufacturer_id' => $medication['manufacturer_id'] ?? null,
                ]
            );


            if (isset($medication['serial_number'], $medication['retail_price'])) {
                $redisData[$medication['serial_number'] . '_medication_price'] = $medication['retail_price'];
            }
        }


        if (!empty($redisData)) {
            Redis::mset($redisData);
        }
    }
}