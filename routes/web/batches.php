<?php

use App\Http\Controllers\BatchController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function() {
    Route::get('/batches/{areaCode}', [BatchController::class, 'index'])->name('batches.index');
    Route::get('/batches/results/{batch}', [BatchController::class, 'show'])->name('batches.show');
    Route::post('/batches/{batch}/authorize', [BatchController::class, 'authorize'])->name('batches.authorize');
    Route::resource('batches', BatchController::class)->except(['index', 'show']);
});
