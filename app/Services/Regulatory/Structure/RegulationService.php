<?php

namespace App\Services\Regulatory\Structure;

use App\Models\Regulatory\Structure\Regulation;
use Illuminate\Support\Collection;

class RegulationService
{
    public function createRegulation(array $data)
    {
        $regulation = new Regulation();
        $regulation->lab_matrix_id = $data['lab_matrix_id'];
        $regulation->observation = $data['observation'];
        $regulation->save();
        return $regulation;
    }
}