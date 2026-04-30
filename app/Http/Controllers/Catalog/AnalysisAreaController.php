<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Catalog\Controller;
use App\Models\Catalog\AnalysisArea;

class AnalysisAreaController extends Controller
{
    protected string $modelClass = AnalysisArea::class;
    protected array $attributes = ['name', 'code'];
}
