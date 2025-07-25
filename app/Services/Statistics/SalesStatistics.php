<?php

namespace App\Services\Statistics;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SalesStatistics
{
    public function getProfitInfo(User $admin, array $payload)
    {
        $totalProfit = 0;
        $totalLoss = 0;

        /** @var \Illuminate\Database\Eloquent\Collection|User[] $staff */
        $staff = $admin->pharmacy->staff;

        $profits = DB::table('carts')
            ->whereIn('user_id', $staff->pluck('id'))
            ->where('created_at', '>=', $payload['scope'])
            ->select('user_id', DB::raw('SUM(total_price - base_price) AS profits'))
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        foreach ($staff as $worker) {
            $workerProfit = $profits->get($worker->id)->profits ?? 0;

            $worker->generated_profit = $workerProfit;
            $totalProfit += $workerProfit;
        }

        return [
            'staff' => $staff,
            'total_profit' => $totalProfit,
            'total_loss' => $totalLoss,
        ];
    }

    public function getProductInfo(User $admin)
    {
        //? most sold med
        $mostSold = DB::selectOne(
            'SELECT 
                ci.product_id, ci.type
                SUM(ci.quantity) AS totat_quantity_sold
            FROM carts cs
            JOIN cart_items ci ON cs.id = ci.cart_id 
            WHERE cs.pharmacy_id = ?
            GROUP BY ci.product_id, ci.type
            ORDER BY total_quantity_sold DESC
            LIMIT 1',
            $admin['pharmacy_id']
        );

        //? least sold med
        $leastSold = DB::selectOne(
            'SELECT 
            ci.product_id, ci.type, 
            SUM(ci.quantity) as total_quantity_sold
        FROM carts cs
        JOIN cart_items ci ON cs.id = ci.cart_id 
        WHERE cs.pharmacy_id = ?
        GROUP BY ci.product_id, ci.type
        ORDER BY total_quantity_sold ASC
        LIMIT 1',
            [$admin['pharmacy_id']]
        );

        //? most profitable med
        $mostProfitable = DB::selectOne(
            'SELECT 
            ci.product_id, ci.type, 
            (SUM(ci.retail_price * ci.quantity) - SUM(ci.purchase_price * ci.quantity)) as total_profit
        FROM carts cs
        JOIN cart_items ci ON cs.id = ci.cart_id 
        WHERE cs.pharmacy_id = ?
        GROUP BY ci.product_id, ci.type
        ORDER BY total_profit DESC
        LIMIT 1',
            [$admin['pharmacy_id']]
        );

        //? least profitable med
        $leastProfitable = DB::selectOne(
            'SELECT 
            ci.product_id, ci.type, 
            (SUM(ci.retail_price * ci.quantity) - SUM(ci.purchase_price * ci.quantity)) as total_profit
        FROM carts cs
        JOIN cart_items ci ON cs.id = ci.cart_id 
        WHERE cs.pharmacy_id = ?
        GROUP BY ci.product_id, ci.type
        ORDER BY total_profit ASC
        LIMIT 1',
            [$admin['pharmacy_id']]
        );

        return [
            'most_sold' => $mostSold,
            'least_sold' => $leastSold,
            'most_profitable' => $mostProfitable,
            'least_profitable' => $leastProfitable,
        ];
    }
}
