<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ReceivingController;
use App\Http\Controllers\Api\AcidTestingController;

Route::get('/receivings', [ReceivingController::class, 'index']);
Route::post('/receivings', [ReceivingController::class, 'store']);
Route::get('/receivings/lot/{lotNo}', [ReceivingController::class, 'getByLot']);
Route::patch('/receivings/{id}/status', [ReceivingController::class, 'updateStatus']);

Route::get('/acid-testings', [AcidTestingController::class, 'index']);
Route::post('/acid-testings', [AcidTestingController::class, 'store']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
