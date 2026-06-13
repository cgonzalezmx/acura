<?php

namespace App\Services\Analyses;

use App\Models\Analysis;
use App\Models\Samples\Sample;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;

class AnalysisService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private UserService $userService
    )
    {}

    public function generateAnalyses(Sample $sample)
    {
        DB::transaction(function() use($sample) {
            $rows = $this->getRows($sample);
            DB::table('analyses')->insert($rows);
        });
    }

    public function addSampleThresholds(Sample $sample)
    {
        $thresholds = $sample->thresholds;
        $sample->analyses->each(function(Analysis $analysis) use($thresholds) {
            $analysisThresholds = $thresholds->where('parameter_id', $analysis->parameter_id)->pluck('id')->all();
            $analysis->thresholds()->sync($analysisThresholds);
        });
    }

    public function listAnalyses(
        User $user,
        string | null $from = null,
        string | null $until = null,
        int | null $state = null,
        string | null $parameter = null
    )
    {
        $analyses = Analysis::with([
                'sample',
                'parameter',
                'thresholds'
            ])
            ->from($from ?? now()->startOfMonth())
            ->until($until ?? now())
            ->addSelect(['total_indexes' => function($query) {
                $query->selectRaw('COUNT(*)')
                    ->from('analyses as sub')
                    ->whereColumn([
                        ['sub.sample_id', '=', 'analyses.sample_id'],
                        ['sub.parameter_id', '=', 'analyses.parameter_id']
                    ]);
            }])
            ->whereHas('parameter', function($query) use ($parameter) {
                $query->whereNotIn('analysis_area_id', [5, 0])
                    ->where('name', $parameter);
            });

        if (!$user->hasRole('admin')) {
            $analyses->whereHas('parameter.analysisArea', function($query) use($user) {
                $query->whereIn('code', $this->userService->analysisAreas($user));
            });
        }

        match ((int) $state) {
            0 => $analyses->where('registered', false),
            1 => $analyses->where('registered', true),
            default => null
        };

        return $analyses->get()->append('isRanged');
    }

    private function getRows(Sample $sample): array
    {
        $sample->loadCount('takes');
        $sample->load('takes');
        $totalTakes = $sample->takes_count;
        $rows = [];
        $sample->entry->parameters
            ->each(function($param) use(&$rows, $totalTakes, $sample) {
                $row = [
                    'parameter_id' => $param->parameter_id,
                    'sample_id' => $sample->id
                ];

                for ($i = 1; $i <= $totalTakes; $i++) {
                    $row['index'] = $i;
                    $row['take_id'] = $sample->takes->where('sequence', $i)->first()->id;
                    $row['lab_matrix_id'] = $sample->matrix->id;
                    $rows[] = $row;

                    if ($param->quantity === 1) {
                        return;
                    }
                }
            });

        return $rows;
    }
}
