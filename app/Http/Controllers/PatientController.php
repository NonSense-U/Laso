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
        $patients = $request->user()->pharmacy->patients;
        return ApiResponse::success('ok', ['patients' => $patients]);
    }

    public function getPatient(Request $request, $patient_id)
    {
        $patient = Patient::findOrFail($patient_id);
        $patient->load('debts');
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


}
