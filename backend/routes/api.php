<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\MasterDistributorController;
use App\Http\Controllers\Api\MasterMerchantController;
use App\Http\Controllers\Api\MasterProductController;
use App\Http\Controllers\Api\MasterVoucherProductController;
use App\Http\Controllers\Api\MockVoucherApiController;
use App\Http\Controllers\Api\ProcessRewardController;
use App\Http\Controllers\Api\RewardController;
use App\Http\Controllers\Api\TransRewardController;
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
    Route::apiResource('prcss-trx', TransRewardController::class);

    Route::get('admin/dashboard',[DashboardController::class, 'adminDashboard']);
});

Route::middleware(['auth:sanctum', 'role:customer'])->group(function () {
    Route::post('customer/prcs-trx',[TransRewardController::class, 'storeCustTrx']);
    Route::get('customer/prcs-trx', [TransRewardController::class, 'myTrx']);

    Route::post('customer/prcs-trx/{transaction}/reward',[TransRewardController::class, 'claimReward']);

    Route::post(
        'customer/rewards/{rewardOrderTrans}/redeem',
        [ProcessRewardController::class, 'redeem']
    );

    Route::get(
        'customer/redemptions',
        [ProcessRewardController::class, 'myRedemptions']
    );
});


Route::middleware(['auth:sanctum', 'role:admin'])->get('/admin-test', function () {
    return response()->json([
        'success' => true,
        'message' => 'You are an admin',
    ]);
});

Route::post(
    'mock-voucher/vouchers/issue',
    [MockVoucherApiController::class, 'issue']
);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/health', function(){
    return response()->json([
        'success' => true,
        'message' => 'ClaimoHub API is running',
    ]);
});
