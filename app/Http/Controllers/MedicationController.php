<?php

namespace App\Http\Controllers;

use App\Models\Medication;
use App\Http\Requests\StoreMedicationRequest;
use App\Http\Requests\UpdateMedicationRequest;
use App\Traits\V1\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MedicationController extends Controller
{
    use ApiResponse;

    private $medicationService;

    public function index()
    {
        $data = Medication::all();
        return ApiResponse::success('Global medications retrieved successfully.', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */

public function addMedication(StoreMedicationRequest $request)
{
    $data = $request->validated();

    $path = 'medications.json';
    $disk = Storage::disk('public');

    $medications = [];
    if ($disk->exists($path)) {
        $medications = json_decode($disk->get($path), true) ?? [];
    }

    $medications[] = $data;

    $disk->put($path, json_encode($medications, JSON_PRETTY_PRINT));

    $medication = Medication::create($request->validated());

    return response()->json([
        'message' => 'Medication added successfully!',
        'medication' => $data,
        'file_path' => asset('storage/' . $path), // link to JSON file
    ]);
}



    /**
     * Display the specified resource.
     */

    public function show(Request $request, int $medication_id)
    {
        $medication = Medication::findOrFail($medication_id);
        return ApiResponse::success('ok', ['medication' => $medication]);
    }

    public function getMedBySerialNumber(Request $request, $serial_number)
    {
        $medication = Medication::query()->where('serial_number', '=', $serial_number)->firstOrFail();
        return ApiResponse::success('ok', ['medication' => $medication]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Medication $medication)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMedicationRequest $request, Medication $medication)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Medication $medication)
    {
        //
    }
}
