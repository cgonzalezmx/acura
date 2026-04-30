<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Catalog\Controller;
use App\Models\Catalog\SamplePreserver;

class SamplePreserverController extends Controller
{
    protected string $modelClass = SamplePreserver::class;
    protected array $attributes = ['name', 'description'];
}
