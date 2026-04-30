<?php

namespace App\Services\Parameters;

use App\Models\Parameter;

class ParameterService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
    }

    public function getByAnalysisArea(array $areas, array $fields = null)
    {
        return Parameter::whereHas('analysisArea', fn($query) => $query->whereIn('code', $areas))
            ->select($fields ?? '*')
            ->distinct()
            ->get();
    }
}
