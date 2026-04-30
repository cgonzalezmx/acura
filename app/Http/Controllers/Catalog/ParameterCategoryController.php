<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Catalog\Controller;
use App\Models\Catalog\ParameterCategory;
use Illuminate\Http\Request;

class ParameterCategoryController extends Controller
{
    protected string $modelClass = ParameterCategory::class;
    protected array $attributes = ['name', 'description'];
}
