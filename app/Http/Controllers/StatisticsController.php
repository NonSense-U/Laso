<?php

namespace App\Http\Controllers;

use App\Services\Statistics\InventoryStatsService;
use App\Services\Statistics\SalesStatsService;
use App\Traits\V1\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StatisticsController extends Controller
{
    use ApiResponse;

    private $salesStatsService, $inventoryStatsService;
    
    public function __construct(SalesStatsService $salesStatsService, InventoryStatsService $inventoryStatsService)
    {
        $this->salesStatsService = $salesStatsService;
        $this->inventoryStatsService = $inventoryStatsService;
    }

    public function getStats(Request $request)
    {
        $validated = $request->validate([
            'scope' => ['required', Rule::in(['today', 'lastWeek', 'lastMonth'])],
        ]);

        $date = match ($validated['scope']) {
            'today' => Carbon::today(),
            'lastWeek' => Carbon::now()->subWeek(),
            'lastMonth' => Carbon::now()->subMonth(),
        };

        $data['profit_section'] = $this->salesStatsService->getProfitInfo($request->user(), $date);
        $data['profit_section']['product_section'] = $this->salesStatsService->getProductInfo($request->user(), $date);
        // TODO
        $data['losses_section'] = $this->inventoryStatsService->expiredMeds($request->user(), $date);
        return ApiResponse::success('ok',$data);
    }
}
