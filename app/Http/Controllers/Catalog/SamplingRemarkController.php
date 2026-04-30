<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Catalog\Controller;
use App\Models\Catalog\SamplingRemark;

class SamplingRemarkController extends Controller
{
    protected string $modelClass = SamplingRemark::class;
    protected array $attributes = ['code', 'description'];
}
