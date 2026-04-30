<?php

use App\Http\Controllers\WorkOrderController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function() {
    Route::resource('work-orders', WorkOrderController::class);
});
