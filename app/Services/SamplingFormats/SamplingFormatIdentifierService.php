<?php

namespace App\Services\SamplingFormats;

use App\Models\SamplingFormat;

class SamplingFormatIdentifierService
{
    public function makeIdentifier(int $clientId, string $year = null): string
    {
        $nextInSequence = SamplingFormat::getMaxYearlySequence() + 1;
        return str_pad($nextInSequence, 4, 0, STR_PAD_LEFT)
            . '-'
            . ($year ?? date('y'));
    }
}
