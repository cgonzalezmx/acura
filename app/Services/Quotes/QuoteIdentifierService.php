<?php

namespace App\Services\Quotes;

use App\Models\Quotes\Quote;

class QuoteIdentifierService
{
    public function getMaxSequenceIndex(): int
    {
        return (int) Quote::where('year', date('Y'))->max('sequence_index');
    }

    public function makeIdentifier(int $currentSequence, string $userAlias, ?string $year = null): string
    {
        $year = $year ?? date('y');
        $userAlias = $userAlias;
        return "{$currentSequence}-{$userAlias}-{$year}";
    }
}