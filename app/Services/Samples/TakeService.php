<?php

namespace App\Services\Samples;

use App\Models\Samples\Take;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TakeService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function store(array | Collection $takes, int $sampleId)
    {
        $rows = collect($takes);
        $rows = $rows->map(fn(array $item) => [
            ...$item,
            'sample_id' => $sampleId
        ]);
        DB::table('takes')->insert($rows->toArray());

        return Take::where('sample_id', $sampleId)->get();
    }

    public function update()
    {}
}
