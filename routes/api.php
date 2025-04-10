<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransferController;
use App\Models\User;
use App\Jobs\TransferJob;

Route::post('/transfer', [TransferController::class, 'transfer']);

Route::post('/dispatch', function () {
    // TransferJob::dispatch(User::find(1), User::find(2), 100.32);

    $transferJob = new TransferJob(User::find(1), User::find(2), 100.32, new \App\Repositories\EventsRepository());
    $transferJob->handle();
});
