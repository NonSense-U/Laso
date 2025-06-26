<?php

use App\Http\Controllers\Accounts\AuthController;
use App\Http\Controllers\Accounts\UserController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\MedPackageController;
use App\Http\Controllers\PackagesOrderController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    dd('smile');
});



//! AUTH ROUTES
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


//! ACCOUNTS ROUTES
Route::post('/admin', [UserController::class, 'createAdmin']);
Route::post('/worker', [UserController::class, 'createWorker'])->middleware('auth:sanctum');


//! SUPPLIER CRU/D
Route::get('/supplier/get', [SupplierController::class, 'index'])->middleware('auth:sanctum');
Route::get('/supplier/get/{supplier_id}', [SupplierController::class, 'show'])->middleware('auth:sanctum');
Route::post('/supplier/create', [SupplierController::class, 'createSupplier'])->middleware('auth:sanctum');
Route::put('/supplier/update/{supplier_id}', [SupplierController::class, 'updateSupplier'])->middleware('auth:sanctum');
// Route::delete('/supplier/delete/{supplier_id}',[SupplierController::class,'deleteSupplier'])->middleware('auth:sanctum'); //! NOT NOW

//! GLOBAL MEDICATIONS
Route::get('/global-meds', [MedicationController::class,'index']);


//! PACKAGES_ORDERS
Route::get('/packages_orders',[PackagesOrderController::class,'index'])->middleware('auth:sanctum');
Route::get('/packages_orders/{packages_order_id}',[PackagesOrderController::class,'show']);


//! MED PACKAGES
Route::get('/med_packages', [MedPackageController::class, 'index'])->middleware('auth:sanctum');
Route::post('/med_packages', [MedPackageController::class,'addMedPackages'])->middleware('auth:sanctum');
