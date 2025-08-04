<?php

namespace App\Http\Controllers;

use App\Http\Requests\DonateMedsRequest;
use App\Services\DonationsService;
use App\Traits\V1\ApiResponse;
use Illuminate\Http\Request;

class DonationsController extends Controller
{
    use ApiResponse;

    private $donationsService;

    public function __construct(DonationsService $donationsService)
    {
        $this->donationsService = $donationsService;
    }

    public function donateMeds(DonateMedsRequest $request)
    {
        $this->donationsService->donateMeds($request->validated());
        return ApiResponse::success('medications has been donated successfully.');
    }

    public function addPublicDonation(Request $request)
    {
        $validated =  $request->validate([
            'amount' => ['required','decimal:0,2', 'min:1']
        ]);
        $this->donationsService->addPublicDonation($validated['amount'], $request->user());
        return ApiResponse::success('Donation done successfully! Thank you.');
    }
}
