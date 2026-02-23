<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ReceivingController;

Route::get('/receivings', [ReceivingController::class, 'index']);
Route::post('/receivings', [ReceivingController::class, 'store']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
