<?php

namespace App\Services\Statistics;

use App\Models\Pharmacy;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Support\Facades\DB;

class LiveStatsService
{
    public function getLiveStats(User $user)
    {

        $pharmacy = $user->pharmacy;
        $staff = $pharmacy->staff()->role('worker')->get();
        $online = [];
        /** @var \App\Models\User $worker  */
        foreach ($staff as $worker) {
            if ($worker->isOnline()) {
                $online[] = $worker;
            }
        }

        $onlineWorkersCount = count($online);

        $treasury = $pharmacy->vaults->keyBy('name');

        $result = DB::select(
            'SELECT SUM(cs.total_retail_price - cs.total_purchase_price) AS earnings FROM carts cs
            WHERE cs.pharmacy_id = ?
            AND cs.created_at > ?
            ',
            [$pharmacy->id, today()]
        );
        $earnings = $result[0]->earnings;

        return [
            'online-workers-num' => $onlineWorkersCount,
            'treasury' => [
                'main' => $treasury->get('main')->balance,
                'charity' => $treasury->get('charity')->balance
            ],
            'earnings' => $earnings,
        ];
    }
}
