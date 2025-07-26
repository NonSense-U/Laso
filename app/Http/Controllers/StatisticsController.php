<?php

namespace App\Http\Controllers;

use App\Services\Statistics\InventoryStatsService;
use App\Services\Statistics\SalesStatsService;
use App\Traits\V1\ApiResponse;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    use ApiResponse;

    private $salesStatsService, $inventoryStatsService;
    
    public function __construct(SalesStatsService $salesStatsService, InventoryStatsService $inventoryStatsService)
    {
        $this->salesStatsService = $salesStatsService;
        $this->inventoryStatsService = $inventoryStatsService;
    }

    public function test(Request $request)
    {
        $date = now()->subWeeks($request->scope);
        $data['profit_section'] = $this->salesStatsService->getProfitInfo($request->user(), $date);
        $data['profit_section']['product_section'] = $this->salesStatsService->getProductInfo($request->user(), $date);
        // TODO
        $data['losses_section'] = $this->inventoryStatsService->expiredMeds($request->user(), $date);
        return ApiResponse::success('ok',$data);
    }
}
