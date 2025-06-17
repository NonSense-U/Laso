<?php

use App\Http\Controllers\Accounts\AuthController;
use App\Http\Controllers\Accounts\UserController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    dd('smile');
});


Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::post('/admin', [UserController::class, 'createAdmin']);
Route::post('/worker', [UserController::class, 'createWorker'])->middleware('auth:sanctum');


Route::get('/supplier/get', [SupplierController::class, 'index'])->middleware('auth:sanctum');
Route::get('/supplier/get/{supplier_id}', [SupplierController::class, 'show'])->middleware('auth:sanctum');
Route::post('/supplier/create', [SupplierController::class, 'createSupplier'])->middleware('auth:sanctum');
Route::put('/supplier/update/{supplier_id}', [SupplierController::class, 'updateSupplier'])->middleware('auth:sanctum');
// Route::delete('/supplier/delete/{supplier_id}',[SupplierController::class,'deleteSupplier'])->middleware('auth:sanctum'); //! NOT NOW
