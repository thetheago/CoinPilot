<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransferController;

Route::post('/transfer', [TransferController::class, 'transfer']);

// Route::post('/dispatch', function () {
//     TransferJob::dispatch(User::find(1), User::find(2), 100);
// });
