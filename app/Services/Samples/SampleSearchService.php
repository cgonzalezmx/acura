<?php

namespace App\Services\Samples;

use App\Http\Resources\Samples\ReportIndexResource;
use App\Models\Samples\Sample;
use App\Models\Quotes\Quote;
use App\Models\Quotes\Client;
use Illuminate\Support\Facades\DB;

class SampleSearchService
{
    public function getReportIndex(array $filters)
    {
        $getUniqueAnalyses = fn($query) => $query->select(DB::raw('count(distinct(parameter_id))'));

        $samples = $this->query($filters, function($query) use ($getUniqueAnalyses) {
            $query->with([
                'takes:id,timestamp,sample_id',
                'quote:notes'
            ]);
            $query->withCount([
                'analyses' => $getUniqueAnalyses,
                'takes',
            ]);
        });

        return ReportIndexResource::collection($samples);
    }

    private function query(array $filters, ?callable $callback)
    {
        if (isset($filters['client'])) {
            return $this->getSamplesByClient($filters, $callback);
        }

        $query = Sample::query();
        $callback && $callback($query);
        $this->filterSamples($query, $filters);

        return $query->get();
    }

    private function filterSamples($query, array $filters)
    {
        $whereClauses = [
            'start' => fn($v) => $query->whereDate('samples.reception_date', '>=', $v),
            'end' => fn($v) => $query->whereDate('samples.reception_date', '<=', $v),
            'sampleId' => fn($v) => $query->where('samples.id', $v),
            'samplingFormatId' => fn($v) => $query->where('samples.sampling_format_id', $v),
        ];

        foreach($filters as $field => $value) {
            $where = $whereClauses[$field] ?? null;
            $where && $where($value);
        }
    }

    private function getSamplesByClient(array $filters, ?callable $callback = null)
    {
        $samplesRelation = ['samples' => function($query) use ($filters, $callback) {
            $callback && $callback($query);
            $this->filterSamples($query, $filters);
        }];
        $clientQuotes = Client::select('quote_id')
            ->whereLike('name', $filters['client'])
            ->get()
            ->pluck('quote_id');

        $quotes = Quote::with($samplesRelation)->select('id')->whereIn('quotes.id', $clientQuotes)->get();

        return $quotes->pluck('samples')->flatten(1);
    }
}
