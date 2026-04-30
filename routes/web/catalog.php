<?php

use App\Http\Controllers\Catalog\AnalysisAreaController;
use App\Http\Controllers\Catalog\LabelColorController;
use App\Http\Controllers\Catalog\LabMatrixController;
use App\Http\Controllers\Catalog\MeasurementUnitController;
use App\Http\Controllers\Catalog\SampleContainerController;
use App\Http\Controllers\Catalog\SamplePreserverController;
use App\Http\Controllers\Catalog\MethodologyController;
use App\Http\Controllers\Catalog\ParameterCategoryController;
use App\Http\Controllers\Catalog\QuoteRemarkController;
use App\Http\Controllers\Catalog\SampleStorageController;
use App\Http\Controllers\Catalog\SamplingRemarkController;
use App\Models\Catalog\AnalysisArea;
use App\Models\Catalog\LabelColor;
use App\Models\Catalog\LabMatrix;
use App\Models\Catalog\MeasurementUnit;
use App\Models\Catalog\Methodology;
use App\Models\Catalog\ParameterCategory;
use App\Models\Catalog\QuoteRemark;
use App\Models\Catalog\SampleContainer;
use App\Models\Catalog\SamplePreserver;
use App\Models\Catalog\SampleStorage;
use App\Models\Catalog\SamplingRemark;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('/catalog')->group(function() {
    Route::get('/', function() {
        $relations = ['createdBy', 'updatedBy'];
        return inertia('Catalog/Index', [
            'analysisAreas' => fn() => AnalysisArea::with($relations)->get(),
            'labMatrices' => fn() => LabMatrix::with($relations)->get(),
            'parameterCategories' => fn() => ParameterCategory::with($relations)->get(),
            'sampleContainers' => fn() => SampleContainer::with($relations)->get(),
            'labelColors' => fn() => LabelColor::with($relations)->get(),
            'samplePreservers' => fn() => SamplePreserver::with($relations)->get(),
            'sampleStorages' => fn() => SampleStorage::with($relations)->get(),
            'methodologies' => fn() => Methodology::with($relations)->get(),
            'measurementUnits' => fn() => MeasurementUnit::with($relations)->get(),
            'samplingRemarks' => fn() => SamplingRemark::with($relations)->get(),
            'quoteRemarks' => fn() => QuoteRemark::with($relations)->get(),
        ]);
    })->name('catalog');
    Route::resources([
        'areas'=> AnalysisAreaController::class,
        'matrices' => LabMatrixController::class,
        'containers' => SampleContainerController::class,
        'preservers' => SamplePreserverController::class,
        'methodologies' => MethodologyController::class,
        'sampling-remarks' => SamplingRemarkController::class,
        'quote-remarks' => QuoteRemarkController::class,
        'measurement-units' => MeasurementUnitController::class,
        'label-colors' => LabelColorController::class,
        'parameter-categories' => ParameterCategoryController::class,
        'storages' => SampleStorageController::class,
    ]);
    Route::get('matrices/{matrix}/parameters', [LabMatrixController::class, 'parameters'])->name('matrices.parameters');
    Route::get('matrices/{matrix}/parameters/output/quote', [LabMatrixController::class, 'quoteParameterIndex'])->name('matrices.parameters.output.quote');
});