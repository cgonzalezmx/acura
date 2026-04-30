<?php

use App\Http\Controllers\Samples\SampleController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function() {
    Route::get('samples/search', [SampleController::class, 'search'])->name('samples.search');
    Route::get('samples/filter', [SampleController::class, 'filter'])->name('samples.filter');
    Route::resource('samples', SampleController::class);
});
