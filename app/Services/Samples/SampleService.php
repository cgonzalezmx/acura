<?php

namespace App\Services\Samples;

use App\Models\Quotes\Client;
use App\Models\Quotes\Quote;
use App\Models\Samples\Sample;
use App\Services\Analyses\AnalysisService;
use Illuminate\Support\Facades\DB;

class SampleService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private AnalysisService $analysisService
    )
    {}

    public function store(array $sample, array $takes)
    {
        $takes = collect($takes)->map(function($item, $index) {
            $item['sequence'] = $index + 1;
            return $item;
        });

        DB::transaction(function() use($sample, $takes) {
            $sample = Sample::create($sample);
            $sample->takes()->createMany($takes->toArray());
            $sample->refresh();
            $this->analysisService->setSample($sample)
                ->generateAnalyses();
        });
    }

    public function update(Sample $sample, array $validated)
    {

        $sampleData = $validated['sample'];
        $takes = $validated['takes'];
        DB::transaction(function() use($sample, $sampleData, $takes) {
            $sample->fill($sampleData);
            $sample->save();
            $this->upsertTakesInSample($sample, $takes);
        });
    }

    private function upsertTakesInSample(Sample $sample, array $takes)
    {
        $takes = collect($takes)->map(function($item, $index) {
            $item['sequence'] = $index + 1;
            return $item;
        });

        $sample->takes()->upsert(
            $takes->toArray(),
            'id',
            [
                'timestamp',
                'color',
                'odour',
                'appearance'
            ]
        );
    }

    public function filter(array $conditions)
    {
        if (isset($conditions['client'])) {
            return $this->getSamplesByClient($conditions['client'], $conditions);
        }

        $query = Sample::query();
        $this->filterSamples($query, $conditions);

        return $query->get();
    }

    private function filterSamples($query, array $conditions)
    {
        $whereClauses = [
            'start' => fn($v) => $query->whereDate('reception_date', '>=', $v),
            'end' => fn($v) => $query->whereDate('reception_date', '<=', $v),
            'sampleId' => fn($v) => $query->where('samples.id', $v),
            'samplingFormatId' => fn($v) => $query->where('samples.sampling_format_id', $v),
        ];

        foreach($conditions as $field => $value) {
            $where = $whereClauses[$field] ?? null;
            isset($where) && $where($value);
        }
    }

    private function getSamplesByClient(string $client, array $conditions)
    {
        $samplesRelation = ['samples' => function($query) use ($conditions) {
            $this->filterSamples($query, $conditions);
        }];
        $clientQuotes = Client::select('quote_id')
            ->whereLike('name', $client)
            ->get()
            ->pluck('quote_id');

        $quotes = Quote::with($samplesRelation)->select('id')->whereIn('quotes.id', $clientQuotes)->get();

        return $quotes->pluck('samples')->flatten(1);
    }
}
