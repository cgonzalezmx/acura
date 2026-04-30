<?php

use App\Http\Controllers\Regulatory\Structure\RegulationController;
use App\Http\Controllers\Regulatory\Instances\NodeController as InstanceNodeController;
use App\Http\Controllers\Regulatory\Instances\ThresholdsController;
use App\Http\Controllers\Regulatory\Structure\BundleController;
use App\Http\Controllers\Regulatory\Structure\NodeController as StructureNodeController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function() {
    Route::prefix('/regulations')->group(function() {
        Route::resource('definitions', RegulationController::class)->names('regulations.definitions');
        Route::resource('bundles', BundleController::class)->names('regulations.bundles');
        Route::post('bundles/{bundle}', [BundleController::class, 'package'])->name('regulations.bundles.package');
        Route::get('/', [StructureNodeController::class, 'workspace'])->name('regulations.tree');
        Route::get('root', [StructureNodeController::class, 'root'])->name('regulations.tree.root');
        Route::resource('nodes', StructureNodeController::class)
            ->names('regulations.nodes')
            ->except('index');
        Route::get('nodes/{node}/edit/children', [StructureNodeController::class, 'childrenForEdit'])->name('regulations.nodes.edit.children');
        Route::get('nodes/{node}/view/children', [StructureNodeController::class, 'childrenForView'])->name('regulations.nodes.view.children');
        Route::post('{regulation}/parameters', [RegulationController::class, 'addParameter'])->name('regulations.parameters');
        Route::patch('{regulation}/observation', [RegulationController::class, 'saveObservation'])->name('regulations.observations');
        Route::resource('instances/nodes', InstanceNodeController::class)->names('regulations.instances.nodes');
        Route::get('instances/nodes/{node}/children', [InstanceNodeController::class, 'children'])->name('regulations.instances.nodes.children');
        Route::resource('thresholds', ThresholdsController::class)->names('regulations.thresholds');
    });
});
