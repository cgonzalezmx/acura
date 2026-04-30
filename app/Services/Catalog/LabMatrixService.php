<?php

namespace App\Services\Catalog;

use App\Models\Catalog\LabMatrix;
use Illuminate\Http\Resources\Json\ResourceCollection;

class LabMatrixService
{
    public function all(): ResourceCollection
    {
        return LabMatrix::all()->toResourceCollection();
    }
}