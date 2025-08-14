<?php

namespace App\Http\Controllers;

use App\Http\Requests\GoofyDonateMedsRequest;
use App\Services\GoofyDonationService;
use App\Traits\V1\ApiResponse;
use Illuminate\Http\Request;

class GoofyDonationController extends Controller
{
    use ApiResponse;
    private $goofyDoncationService;

    public function __construct(GoofyDonationService $goofyDonationService)
    {
        $this->goofyDoncationService = $goofyDonationService;
    }

    public function donateMeds(GoofyDonateMedsRequest $request)
    {
        $this->goofyDoncationService->donateMeds($request->validated(), $request->user());
        return ApiResponse::success('medications has been donated successfully.');
    }
}
