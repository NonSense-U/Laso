<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInsuranceRequest;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\Insurance;
use App\Models\Patient;
use App\Services\PatientService;
use App\Traits\V1\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    use ApiResponse;
    private $patientService;

    public function __construct(PatientService $patientService)
    {
        $this->patientService = $patientService;
    }
    
    public function index(Request $request)
    {
        $patients = Patient::query()->with('debts')->get();
        return ApiResponse::success('ok', ['patients' => $patients]);
    }
    public function getPatient(Request $request, $patient_id)
    {
        $patient = Patient::query()->where('id', '=', $patient_id)->with('debts')->get();
        return ApiResponse::success('ok', ['patient' => $patient]);
    }


    public function storePatient(StorePatientRequest $request)
    {
        $patient = $this->patientService->storePatient($request->validated(), $request->user());
        return ApiResponse::success('ok', ['patient' => $patient], 201);
    }

    public function updatePatient(UpdatePatientRequest $request, $patient_id)
    {
        $patient = Patient::findOrFail($patient_id);
        $patient->update($request->validated());
        return ApiResponse::success('ok', ['patient' => $patient]);
    }

    public function deletePatient(Request $request, $patient_id)
    {
        $patient = Patient::findOrFail($patient_id);
        $patient->delete();
        return;
    }

    public function payDebt(Request $request, $debt_id)
    {
        $debt = $this->patientService->payDebt($debt_id);
        return ApiResponse::success("debt paid successfully", ['debt' => $debt], 200);
    }

    public function deleteDebt(Request $request, $debt_id)
    {
        $this->patientService->deleteDebt($debt_id);
        return;
    }
}
