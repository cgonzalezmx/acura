<?php

use App\Http\Controllers\Quotes\ClientController;
use App\Http\Controllers\Quotes\QuoteController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function() {
    Route::post('/quotes/copy/{quote}', [QuoteController::class, 'copy'])->name('quotes.copy');
    Route::get('/quotes/{quote}/entries', [QuoteController::class, 'entries'])->name('quotes.entries');
    Route::get('/quotes/client/', [ClientController::class, 'search'])->name('quotes.clients.search');
    Route::post('/quotes/authorize/{quote}', [QuoteController::class, 'authorize'])->name('quotes.authorize');
    Route::resource('quotes', QuoteController::class);
});
