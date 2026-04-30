<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasYearlySequence
{
    public static function getMaxYearlySequence(string $year = null): int
    {
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query =  self::query();

        $maxIndex = $query
            ->where('year', $year ?? date('Y'))
            ->max('sequence_index');

        return $maxIndex ?? 0;
    }
}