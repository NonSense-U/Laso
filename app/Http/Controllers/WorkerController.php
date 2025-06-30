<?php

namespace App\Http\Controllers;

use App\Services\WorkerService;
use App\Traits\V1\ApiResponse;
use Illuminate\Http\Request;

class WorkerController extends Controller
{
    use ApiResponse;

    private $workerService;

    public function __construct(WorkerService $workerService)
    {
        $this->workerService = $workerService;
    }

}
