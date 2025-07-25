<?php

namespace App\Http\Controllers;

use App\Services\Statistics\SalesStatistics;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    private $salesStatsService;
    
    public function __construct(SalesStatistics $salesStatsService)
    {
        $this->salesStatsService = $salesStatsService;
    }

    public function test(Request $request)
    {
        $this->salesStatsService->getProductInfo($request->user());
    }
}
