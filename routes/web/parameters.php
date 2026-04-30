<?php

use App\Http\Controllers\ParameterController;
use App\Http\Controllers\Parameters\GroupController;
use App\Http\Controllers\Parameters\SyncParameterToGroupContoller;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function() {
    Route::resource('parameters', ParameterController::class);
    Route::resource('parameter-groups', GroupController::class);
    Route::post('parameter-groups/{groupId}', SyncParameterToGroupContoller::class)->name('parameter-groups.sync');
});