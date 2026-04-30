<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::inertia('/', 'Home/Main')->name('home');
});

require __DIR__.'/auth.php';
require __DIR__.'/web/parameters.php';
require __DIR__.'/web/catalog.php';
require __DIR__.'/web/regulatory.php';
require __DIR__.'/web/quotes.php';
require __DIR__.'/web/clients.php';
require __DIR__.'/web/users.php';
require __DIR__.'/web/sampling.php';
require __DIR__.'/web/samples.php';
require __DIR__.'/web/analyses.php';
require __DIR__.'/web/batches.php';
require __DIR__.'/web/reports.php';
