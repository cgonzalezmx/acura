<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Catalog\Controller;
use App\Models\Catalog\LabelColor;

class LabelColorController extends Controller
{
    protected string $modelClass = LabelColor::class;
    protected array $attributes = ['color'];
}
