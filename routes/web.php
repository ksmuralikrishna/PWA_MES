<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ReceivingController;

Route::get('/receiving', function () {
    return view('receiving'); // Laravel will look for resources/views/receiving.blade.php
});
