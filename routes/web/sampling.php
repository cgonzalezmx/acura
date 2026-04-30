<?php

use App\Http\Controllers\SamplingFormatController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function() {
    Route::get('sampling-formats/{samplingFormat:identifier}.pdf', [SamplingFormatController::class, 'show'])->name('sampling-formats.show');
    Route::get('sampling-formats/{identifier}/details', [SamplingFormatController::class, 'details'])->name('sampling-formats.details');
    Route::get('sampling-formats/search', [SamplingFormatController::class, 'search'])->name('sampling-formats.search');
    Route::resource('sampling-formats', SamplingFormatController::class)->except('show');
});
