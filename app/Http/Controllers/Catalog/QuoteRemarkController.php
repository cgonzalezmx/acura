<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Catalog\Controller;
use App\Models\Catalog\QuoteRemark;

class QuoteRemarkController extends Controller
{
    protected string $modelClass = QuoteRemark::class;
    protected array $attributes = ['code', 'description'];
}
