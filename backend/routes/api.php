<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MasterDistributorController;
use App\Http\Controllers\Api\MasterMerchantController;
use App\Http\Controllers\Api\MasterProductController;
use App\Http\Controllers\Api\MasterVoucherProductController;
use App\Http\Controllers\Api\RewardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function() {
    Route::post('/register', [AuthController::class, 'storeRegister']);
    Route::post('/login', [AuthController::class, 'storeLogin']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('mst-distributor/merchants',[MasterDistributorController::class, 'merchants']);

    Route::apiResource('mst-distributor', MasterDistributorController::class);
    Route::apiResource('mst-merchant', MasterMerchantController::class);
    Route::apiResource('mst-product', MasterProductController::class);
    Route::apiResource('mst-vch-product', MasterVoucherProductController::class);
    Route::apiResource('mst-reward', RewardController::class);
});


Route::middleware(['auth:sanctum', 'role:admin'])->get('/admin-test', function () {
    return response()->json([
        'success' => true,
        'message' => 'You are an admin',
    ]);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/health', function(){
    return response()->json([
        'success' => true,
        'message' => 'ClaimoHub API is running',
    ]);
});
