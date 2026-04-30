<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Catalog\Controller;
use App\Models\Catalog\MeasurementUnit;

class MeasurementUnitController extends Controller
{
    protected string $modelClass = MeasurementUnit::class;
    protected array $attributes = ['unit'];
}
