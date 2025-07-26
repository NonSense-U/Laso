<?php

namespace App\Services\Statistics;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class SalesStatsService
{
    public function getProfitInfo(User $admin, $date)
    {
        $totalProfit = 0;

        /** @var \Illuminate\Database\Eloquent\Collection|User[] $staff */
        $staff = $admin->pharmacy->staff;

        if ($staff->isEmpty()) {
            return [
                'highest_profit_worker' => null,
                'staff' => collect(),
                'total_profit' => 0,
            ];
        }

        $profitRows = DB::table('carts')
            ->whereIn('user_id', $staff->pluck('id'))
            ->where('created_at', '>=', $date)
            ->select('user_id', DB::raw('SUM(total_price - base_price) AS profits'))
            ->groupBy('user_id')
            ->orderByDesc('profits')
            ->get();

        $profits = $profitRows->keyBy('user_id');

        foreach ($staff as $worker) {
            $workerProfit = $profits->get($worker->id)->profits ?? 0;
            $worker->generated_profit = $workerProfit;
            $totalProfit += $workerProfit;
        }

        $topProfitRow = $profitRows->first();
        $highestProfitWorker = $staff->firstWhere('id', $topProfitRow->user_id ?? null);

        return [
            'total_profit' => $totalProfit,
            'highest_profit_worker' => $highestProfitWorker,
            'staff' => $staff,
        ];
    }


    public function getProductInfo(User $admin, $date)
    {
        //? most sold med
        $mostSold = DB::selectOne(
            'SELECT 
                ci.product_id,
                ci.type,
                SUM(ci.quantity) AS total_quantity_sold
            FROM carts cs
            JOIN cart_items ci ON cs.id = ci.cart_id 
            WHERE cs.pharmacy_id = ? AND cs.created_at > ?
            GROUP BY ci.product_id, ci.type
            ORDER BY total_quantity_sold DESC
            LIMIT 1',
            [$admin['pharmacy_id'], $date]
        );


        //? least sold med
        $leastSold = DB::selectOne(
            'SELECT 
            ci.product_id, ci.type, 
            SUM(ci.quantity) as total_quantity_sold
            FROM carts cs
            JOIN cart_items ci ON cs.id = ci.cart_id 
            WHERE cs.pharmacy_id = ? AND cs.created_at > ?
            GROUP BY ci.product_id, ci.type
            ORDER BY total_quantity_sold ASC
            LIMIT 1',
            [$admin['pharmacy_id'], $date]

        );

        //? most profitable med
        $mostProfitable = DB::selectOne(
            'SELECT 
            ci.product_id, ci.type, 
            (SUM(ci.retail_price * ci.quantity) - SUM(ci.purchase_price * ci.quantity)) as total_profit
        FROM carts cs
        JOIN cart_items ci ON cs.id = ci.cart_id 
        WHERE cs.pharmacy_id = ? AND cs.created_at > ?
        GROUP BY ci.product_id, ci.type
        ORDER BY total_profit DESC
        LIMIT 1',
            [$admin['pharmacy_id'], $date]

        );

        //? least profitable med
        $leastProfitable = DB::selectOne(
            'SELECT 
            ci.product_id, ci.type, 
            (SUM(ci.retail_price * ci.quantity) - SUM(ci.purchase_price * ci.quantity)) as total_profit
        FROM carts cs
        JOIN cart_items ci ON cs.id = ci.cart_id 
        WHERE cs.pharmacy_id = ? AND cs.created_at > ?
        GROUP BY ci.product_id, ci.type
        ORDER BY total_profit ASC
        LIMIT 1',
            [$admin['pharmacy_id'], $date]
        );

        return [
            'most_sold_item' => $mostSold,
            'least_sold_item' => $leastSold,
            'most_profitable_item' => $mostProfitable,
            'least_profitable_item' => $leastProfitable,
        ];
    }
}
