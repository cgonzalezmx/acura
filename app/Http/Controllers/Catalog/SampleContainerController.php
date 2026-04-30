<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Catalog\Controller;
use App\Models\Catalog\SampleContainer;

class SampleContainerController extends Controller
{
    protected string $modelClass = SampleContainer::class;
    protected array $attributes = ['name', 'description'];
}
