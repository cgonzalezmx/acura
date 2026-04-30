<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Catalog\Controller;
use App\Http\Resources\Parameters\QuoteResource;
use App\Models\Catalog\LabMatrix;
use App\Http\Resources\Parameters\RegulationResource as ParameterResource;
use Str;

class LabMatrixController extends Controller
{
    protected string $modelClass = LabMatrix::class;
    protected array $attributes = ['name', 'code'];

    public function index() {
        return LabMatrix::all()->toResourceCollection();
    }

    public function parameters(LabMatrix $matrix) {
        return ParameterResource::collection($matrix->parameters);
    }

    public function quoteParameterIndex(LabMatrix $matrix)
    {
        $parameters = $matrix->parameters->sortBy(fn($item) => Str::ascii($item['name']));
        return QuoteResource::collection($parameters->all());
    }
}
