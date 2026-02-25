<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ReceivingController;
use App\Http\Controllers\Api\AcidTestingController;

Route::get('/', function () {
    return file_get_contents(public_path('index.html'));
});

Route::get('/receiving', function () {
    return view('receiving'); // Laravel will look for resources/views/receiving.blade.php
});

Route::get('/acid-testing', function () {
    return view('acid_testing'); // Laravel will look for resources/views/acid_testing.blade.php
});