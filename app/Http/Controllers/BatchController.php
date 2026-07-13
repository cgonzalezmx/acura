<?php

namespace App\Http\Controllers;

use App\Http\Resources\Catalog\AnalysisAreaResource;
use App\Http\Requests\StoreBatchRequest;
use App\Http\Resources\Analyses\AnalysisResource;
use App\Models\Analysis;
use App\Models\Batch;
use App\Models\Catalog\AnalysisArea;
use App\Models\Catalog\SampleStorage;
use App\Models\Parameter;
use App\Models\Samples\Threshold;
use App\Services\Batches\BatchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BatchController extends Controller
{
    public function __construct(
        private BatchService $batchService
    ){}

    public function index(string $areaCode, Request $request)
    {
        $analysisAreas = AnalysisArea::select(['id', 'name', 'code'])->get();
        $area = $analysisAreas->where('code', $areaCode)->first();
        $area->load(['parameters' => function($query) {
            $query->select(['id', 'name', 'analysis_area_id']);
            $query->orderBy('name');
        }]);
        $parameters = $area->parameters->unique('name')->pluck('name')->all();

        return inertia('Batches/Index', [
            'area' => new AnalysisAreaResource($area),
            'parameters' => $parameters,
            'batches' => Inertia::optional(fn() => $this->search(
                $request->str('selection'),
                $request->integer('status', 0),
                $area->id
            ))
        ]);
    }

    public function show(string $id)
    {
        $batch = Batch::withCount('analyses as analysis_count')->with(['analysisArea', 'sampleStorages'])->find($id);
        $analyses = $batch
            ->analyses()
            ->with([
                'sample:id,identifier,sampling_format_id',
                'sample.entry:quote_entries.id,title',
                'thresholds',
                'parameter:id,measurement_unit_id,quantification_low_range,quantification_mid_range,quantification_high_range',
                'parameter.measurementUnit'])
            ->select(['analyses.id', 'analyses.index', 'analyses.params', 'analyses.result', 'analyses.reported_result', 'analyses.sample_id', 'parameter_id'])
            ->get();
        $refrigerators = SampleStorage::select(['id', 'identifier'])->whereNot('id', 0)->get();
        $reference = $analyses[0]->parameter;
        $measurementUnit = $reference->measurementUnit->unit;
        return inertia('Batches/Batch', [
            'batch' => $batch,
            'analyses' => AnalysisResource::collection($analyses),
            'refrigerators' => $refrigerators,
            'min_quantifiable' => $this->minQuantifiable($batch, $analyses[0]->parameter),
            'measurement_unit' => $measurementUnit,
        ]);
    }

    public function store(Request $request)
    {
        $this->batchService->createService($request->input());
    }

    public function update(Batch $batch, StoreBatchRequest $request)
    {
        $validated = $request->validated();
        $batchPayload = [
            ...$validated['batch'],
        ];

        if (isset($validated['params'])) {
            $batchPayload['params'] = $validated['params'];
        }

        if (isset($validated['controls'])) {
            $batchPayload['controls'] = $validated['controls'];
        }
        DB::transaction(function() use($batch, $batchPayload, $validated, $request) {
            $analyzed_at = $request->array('batch')['analyzed_at'];
            $batch->update($batchPayload);
            $userId = $request->user()->id;

            foreach ($validated['analyses'] as $analysis) {
                Analysis::find($analysis['id'])
                    ->update([
                        ...$analysis,
                        'params' => $analysis['params'],
                        'analyzed_at' => $analyzed_at,
                        'analyzed_by' => $userId
                    ]);
            }
        });
    }

    public function authorize(Batch $batch, Request $request)
    {
        DB::transaction(function() use($batch, $request) {
            $userId = $request->user()->id;
            $this->resolveVeredict($batch);

            $batch->authorized = true;
            $batch->save();
            $batch->analyses()->update([
                'authorized' => true,
                'authorized_at' => now(),
                'authorized_by' => $userId,
                'range' => $batch->range,
                'log' => $batch->log,
                'method' => DB::table('methodologies')
                    ->join('parameters', 'parameters.methodology_id', '=', 'methodologies.id')
                    ->whereColumn('parameters.id', 'analyses.parameter_id')
                    ->select('methodologies.name')
                    ->limit(1)
            ]);
        });
    }

    private function resolveVeredict(Batch $batch)
    {
        $updateValues = [];
        $analyses = $batch->analyses()
            ->select('sample_id', 'parameter_id', 'result')
            ->get();

        $test = DB::table('sample_thresholds as st')
            ->join('analyses as a', 'st.parameter_id', '=', 'a.parameter_id')
            ->whereIn('st.sample_id', $analyses->pluck('sample_id'))
            ->whereIn('st.parameter_id', $analyses->pluck('parameter_id'))
            ->select([
                DB::raw("
                    CASE
                        WHEN st.max IN ('N.A.', 'N.E.') THEN 2
                        WHEN CAST(a.result AS FLOAT) BETWEEN st.min AND st.max THEN 1
                        WHEN st.max > CAST(a.result AS FLOAT) THEN 1
                        ELSE 0
                    END as veredict
                ")
            ]);
        dd($test->get());
    }

    private function minQuantifiable(Batch $batch, Parameter $parameter)
    {
        $fromBatch = $batch->minimal_quantification;

        if ($fromBatch) {
            return $fromBatch;
        }

        $byRange = [
            'low' => $parameter->quantification_low_range,
            'mid' => $parameter->quantification_mid_range,
            'high' => $parameter->quantification_high_range,
        ];

        return $byRange[$batch->range];
    }

    private function search(string $parameter, int $status, int $area)
    {
        return Batch::withCount('analyses')
            ->where('parameter', $parameter)
            ->where('analysis_area_id', $area)
            ->get();
    }
}
