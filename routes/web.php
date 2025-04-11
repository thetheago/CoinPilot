<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/horizon/dashboard');
});

Route::get('/health', function () {
    return response()->json(['status' => 'ok'], 200);
});