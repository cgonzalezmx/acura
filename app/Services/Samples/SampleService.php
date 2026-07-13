<?php

namespace App\Services\Samples;

use App\Models\Quotes\Client;
use App\Models\Quotes\Quote;
use App\Models\Quotes\Threshold as ReportThreshold;
use App\Models\Samples\Sample;
use App\Models\Samples\Threshold as SampleThreshold;
use App\Models\SamplingFormat;
use App\Services\Analyses\AnalysisService;
use App\Traits\ResolvesNumericExpression;
use Illuminate\Support\Facades\DB;

class SampleService
{
    use ResolvesNumericExpression;
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
        $entryId = SamplingFormat::select(['entry_id'])
            ->find($sample['sampling_format_id'])
            ->entry_id;

        DB::transaction(function() use($entryId, $sample, $takes) {
            $sample = Sample::create([...$sample, 'entry_id' => $entryId]);
            $sample->save();
            $sample->takes()->createMany($takes->toArray());
            $sample->refresh();
            $this->generateThresholds($sample);
            $sample->refresh();
            $this->analysisService->generateAnalyses($sample);
            $sample->refresh();
            $this->analysisService->addSampleThresholds($sample);
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

    public function getRelations()
    {
        return [
            'takes',
            'quote',
            'thresholds',
            'analyses' => function($query) {
                $query->with([
                    'analyzedBy',
                    'parameter' => function($subQuery) {
                        $subQuery->join('analyses', function($join) {
                            $join->on('parameters.id', '=', 'analyses.parameter_id');
                        });
                        $subQuery->join('measurement_units', function($join) {
                            $join->on('parameters.measurement_unit_id', '=', 'measurement_units.id');
                        });
                        $rawQuantification = DB::raw("
                            CASE analyses.range
                                WHEN 'low' THEN parameters.quantification_low_range
                                WHEN 'high' THEN parameters.quantification_high_range
                                ELSE parameters.quantification_mid_range
                            END as quantification
                        ");
                        $rawUncertainty = DB::raw("
                            CASE analyses.range
                                WHEN 'low' THEN parameters.uncertainty_low_range
                                WHEN 'high' THEN parameters.uncertainty_high_range
                                ELSE parameters.uncertainty_mid_range
                            END as uncertainty
                        ");
                        $subQuery->select([
                            'parameters.id as id',
                            'parameters.name as name',
                            'measurement_units.unit as unit',
                            $rawQuantification,
                            $rawUncertainty,
                        ]);
                    },
                ]);
            },
        ];
    }

    private function generateThresholds(Sample $sample)
    {
        $sample->with(['reports:id']);
        $reportIds = $sample->reports->pluck('id');
        $reportThresholds = ReportThreshold::whereIn('report_id', $reportIds)->get();
        $sampleThresholds = [];
        $reportThresholds
            ->select(['min', 'max', 'letter', 'parameter_id'])
            ->each(function($threshold) use(&$sampleThresholds) {
                $values = [...$threshold];

                if ($min = $this->resolveNumericExpession($threshold['min'] ?? null, step: 0.000001)) {
                    $values['min_numeric_value'] = $min[1];
                }

                $max = $threshold['max'];

                if (!in_array($max, ['N.A.', 'N.E.'])) {
                    $values['max_numeric_value'] = $this->resolveNumericExpession($max, step: 0.000001)[1];
                }

                $sampleThresholds[] = SampleThreshold::create($values);
            });
        $sample->thresholds()->saveMany($sampleThresholds);
    }
}
