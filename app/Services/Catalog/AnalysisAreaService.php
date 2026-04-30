<?php

namespace App\Services\Catalog;

use App\Models\Catalog\AnalysisArea;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AnalysisAreaService
{
    public function all(): ResourceCollection
    {
        return AnalysisArea::all()->toResourceCollection();
    }

    public function asMap()
    {
        return AnalysisArea::all()
            ->toResourceCollection()
            ->collection
            ->map(fn($area) => [$area->id, $area->name]);
    }
}