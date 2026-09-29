<?php

use App\Http\Controllers\Api\AuthController;
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
