<?php

namespace App\Services\Samples;

use App\Models\Samples\Sample;
use Illuminate\Support\Facades\DB;

class SampleCreator
{
    public function store(array $sample, array $takes)
    {
        $takes = collect($takes)->map(function($item, $index) {
            $item['sequence'] = $index + 1;
            return $item;
        });

        DB::transaction(function() use($sample, $takes) {
            $sample = Sample::create($sample);
            $sample->takes()->createMany($takes->toArray());
        });
    }
}