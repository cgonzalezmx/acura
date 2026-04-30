<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Catalog\Controller;
use App\Models\Catalog\Methodology;

class MethodologyController extends Controller
{
    protected string $modelClass = Methodology::class;
    protected array $attributes = ['name'];
}
