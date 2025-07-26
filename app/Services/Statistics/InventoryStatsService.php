<?php

namespace App\Services\Statistics;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class InventoryStatsService
{
    public function expiredMeds(User $admin, $date)
    {
        $pharmacyId = $admin->pharmacy_id;

        $expiredMeds = DB::select(
            'SELECT mp.id,
                    md.name,
                    mp.created_at AS date_added,
                    mp.base_price AS purchase_price,
                    md.price AS retail_price,
                    mp.base_price AS purchase_price,
                    mp.quantity,
                    (mp.quantity * mp.base_price) AS total_loss
            FROM med_packages mp
            JOIN medications md ON mp.medication_id = md.id
            WHERE mp.pharmacy_id = ?
            AND mp.expiration_date > ?
            AND mp.expiration_date < CURRENT_TIMESTAMP
            ORDER BY total_loss',
            [$pharmacyId, $date]
        );

        $totalLossResult = DB::selectOne(
            'SELECT SUM(mp.quantity * mp.base_price) as total_loss
         FROM med_packages mp
         WHERE mp.pharmacy_id = ?
           AND mp.expiration_date > ?
           AND mp.expiration_date < CURRENT_TIMESTAMP',
            [$pharmacyId, $date]
        );

        $highest_loss = DB::selectOne(
            //TODO
            //! IS THE LOSS BASED ON BASE_PRICE??
            //? ADD AVG CONSUMPTION
            'SELECT mp.medication_id,
                    md.name,
                    SUM(mp.base_price * mp.quantity) AS total_loss,
                    SUM(mp.quantity) AS total_quantity
                    FROM med_packages mp
                    JOIN medications md ON mp.medication_id = md.id
                    WHERE mp.pharmacy_id = ?
                    AND mp.expiration_date > ?
                    AND mp.expiration_date < CURRENT_TIMESTAMP
                    GROUP BY mp.medication_id',
                    [$pharmacyId, $date]
        );

        collect($expiredMeds)->toArray();

        return ['total_loss' => $totalLossResult->total_loss, 'highest_loss'=> $highest_loss, 'expired_meds' => $expiredMeds];
    }
}
