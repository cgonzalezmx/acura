<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Catalog\Controller;
use App\Models\Catalog\SampleStorage;

class SampleStorageController extends Controller
{
    protected string $modelClass = SampleStorage::class;
    protected array $attributes = ['identifier', 'description'];
}
