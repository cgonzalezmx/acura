<?php

namespace App\Services\Batches;

use App\Models\Analysis;
use App\Models\Batch;
use Illuminate\Support\Facades\DB;

class BatchService
{
    private function getPrefix(string $parameter)
    {
        $words = collect(explode(' ', $parameter));
        $prefix = '';

        if ($words->count() > 2) {
            $filtered = $words->filter(function($w) {
                return !in_array(mb_strtolower($w), ['de']);
            });

            $prefix = $filtered->reduce(function($pfx, $word) {
                return $pfx . mb_strtoupper($word[0]);
            }, '');
        }
        else {
            $prefix = $words->first();
            $prefix = mb_substr($prefix, 0, 3);
        }

        $date = now()->format('ymd');

        return $prefix . $date;
    }

    private function getMatrix(array $items)
    {
        $analyses = Analysis::with(['parameter:id,lab_matrix_id', 'parameter.labMatrix:id,code'])
            ->select(['id', 'parameter_id'])
            ->whereIn('id', $items)
            ->get();
        $matrix = $analyses->first()->parameter->labMatrix->code;
        return $analyses->every(fn($item) => $item->parameter->labMatrix->code === $matrix)
            ? $matrix
            : 'mixed';
    }

    private function getAnalysisAreaId($analysisId)
    {
        return Analysis::with('parameter:id,analysis_area_id')->find($analysisId)->parameter->analysis_area_id;
    }

    public function createService(array $data)
    {
        DB::transaction(function() use($data) {
            $batch = collect($data)
                ->except('analyses');
            $batch->put('name', $this->getPrefix($batch['parameter']));
            $batch->put('matrix', $this->getMatrix($data['analyses']));
            $batch->put('analysis_area_id', $this->getAnalysisAreaId($data['analyses'][0]));
            $batch = Batch::create($batch->toArray());
            $batch->analyses()->sync($data['analyses']);
            $batch->analyses()->update(['registered' => true]);
            $batch->analyses()->increment('registration_counter');
        });
    }
}
