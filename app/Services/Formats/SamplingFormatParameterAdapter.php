<?php

namespace App\Services\Formats;

use App\Models\Quotes\Entry;
use App\Models\Quotes\Parameter;

class SamplingFormatParameterAdapter
{
    public function process(Entry $entry)
    {
        return $entry
            ->parameters()
            ->whereNotIn('parameter_id', [105, 355, 356, 554, 593])
            ->get()
            ->map(function(Parameter $parameter) {
            $systemInfo = $parameter->systemInfo;
                return [
                    ...$systemInfo->toArray(),
                    'quantity' => $parameter->quantity,
                    'sample_container' => $systemInfo->sampleContainer->name,
                    'sample_preserver' => $systemInfo->samplePreserver->name,
                    'sampling_remarks' => $systemInfo->samplingRemarks
                ];
            });
    }
}