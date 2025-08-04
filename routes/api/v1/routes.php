<?php

use App\Http\Controllers\Accounts\AuthController;
use App\Http\Controllers\Accounts\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DonationsController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\FastSellingItemController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\MedPackageController;
use App\Http\Controllers\PackagesOrderController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\WorkerController;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    dd('smile');
});


//! AUTH ROUTES
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/forgot-password', [PasswordController::class, 'sendPasswordResetEmail']);
Route::post('/verify-code', [PasswordController::class, 'verifyCode']);
Route::post('/change-password', [PasswordController::class, 'changePassword'])->middleware('auth:sanctum');


//! ACCOUNTS ROUTES
Route::post('/admin', [UserController::class, 'createAdmin']);
Route::post('/worker', [UserController::class, 'createWorker']);
Route::get('/my-profile', [UserController::class, 'getProfile'])->middleware('auth:sanctum');

//! SUPPLIER CRU/D
Route::get('/supplier/get', [SupplierController::class, 'index'])->middleware('auth:sanctum');
Route::get('/supplier/get/{supplier_id}', [SupplierController::class, 'show'])->middleware('auth:sanctum');
Route::post('/supplier/create', [SupplierController::class, 'createSupplier'])->middleware('auth:sanctum');
Route::put('/supplier/update/{supplier_id}', [SupplierController::class, 'updateSupplier'])->middleware('auth:sanctum');
Route::get('/supplier/record/{supplier_id}', [SupplierController::class, 'getSupplierRecord'])->middleware('auth:sanctum');
// Route::delete('/supplier/delete/{supplier_id}',[SupplierController::class,'deleteSupplier'])->middleware('auth:sanctum'); //! NOT NOW

//! GLOBAL MEDICATIONS
Route::get('/global-meds', [MedicationController::class, 'index']);
Route::get('/global-meds/show/{medication_id}', [MedicationController::class, 'show'])->middleware('auth:sanctum');
Route::get('/global-meds/{serial_number}', [MedicationController::class, 'getMedBySerialNumber'])->middleware('auth:sanctum');

//! PACKAGES_ORDERS
Route::get('/packages_orders', [PackagesOrderController::class, 'index'])->middleware('auth:sanctum');
Route::get('/packages_orders/{packages_order_id}', [PackagesOrderController::class, 'show']);

//! RETURN MEDS
Route::post('/return-meds', [MedPackageController::class, 'returnMeds'])->middleware('auth:sanctum');

//! MED PACKAGES
Route::get('/med_packages', [MedPackageController::class, 'index'])->middleware('auth:sanctum');


//! FAST SELLING ITEMS
Route::get('/fast_selling_items', [FastSellingItemController::class, 'index'])->middleware('auth:sanctum');
Route::post('/fast_selling_items', [FastSellingItemController::class, 'addItem'])->middleware('auth:sanctum');
Route::put('/fast_selling_items/{item_id}', [FastSellingItemController::class,'updateItem'])->middleware('auth:sanctum');


//! ADMIN
Route::post('/send-invitation', [EmailController::class, 'sendInvitation'])->middleware('auth:sanctum');
Route::delete('/admin/disable-worker/{worker_id}', [AdminController::class, 'disableWorker'])->middleware('auth:sanctum');
Route::post('/admin/enable-worker/{worker_id}', [AdminController::class, 'enableWorker'])->middleware('auth:sanctum');
Route::get('/admin/my-staff', [AdminController::class,'getWorkers'])->middleware('auth:sanctum');
Route::get('/my-treasury', [PharmacyController::class, 'getTreasury'])->middleware('auth:sanctum');
    //* EXPENSES
Route::post('/draw-expenses', [AdminController::class, 'drawExpenses'])->middleware('auth:sanctum');
Route::get('/get-expenses', [AdminController::class, 'getExpenses'])->middleware('auth:sanctum');

//! DONATIONS
Route::post('/donate-meds', [DonationsController::class, 'donateMeds'])->middleware('auth:sanctum');
Route::post('/add-donation', [DonationsController::class, 'addPublicDonation'])->middleware('auth:sanctum');


//! STORAGE
Route::get('/get-storage', [MedPackageController::class, 'showStorage'])->middleware('auth:sanctum');
Route::get('/expired-meds', [MedPackageController::class, 'expiredMeds'])->middleware('auth:sanctum');


//! TO FOLLOW ON-DUTY STAFF
Route::middleware(['auth:sanctum','update.last.seen'])->group(function(){
    //! Sales
    Route::post('/sales/checkout', [WorkerController::class, 'checkout']);

    //* UPDATE URL LATER
    //TODO
    //! Add Medication Order
    Route::post('/med_packages', [MedPackageController::class, 'addMedPackages']);
});

Route::get('/stats', [StatisticsController::class, 'getStats'])->middleware('auth:sanctum');

//! LOGS
Route::get('/meds-logs', [MedPackageController::class, 'MedsLogs'])->middleware('auth:sanctum');