<?php

namespace App\Services\Analyses;

use App\Models\Analysis;
use App\Models\Samples\Sample;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;

class AnalysisService
{
    private Sample $sample;
    /**
     * Create a new class instance.
     */
    public function __construct(
        private UserService $userService
    )
    {}

    public function setSample(Sample $sample)
    {
        $this->sample = $sample;
        return $this;
    }

    public function generateAnalyses()
    {
        DB::transaction(function() {
            $this->insert();
            $analyses = $this->sample->analyses()->select('id', 'parameter_id')->get();
            $reports = $this->reports()
                ->pluck('thresholds')
                ->map(function($item) use($analyses) {
                    return $item->whereIn('parameter_id', $analyses->pluck('parameter_id'))
                    ->select(['id', 'parameter_id'])
                    ->map(fn($item) => [$item['parameter_id'], $item['id']]);
                });

            $analyses = $analyses->mapToGroups(fn($item) => [$item->parameter_id => $item->id]);
            $analyses = $analyses->toArray();
            $rows = [];

            foreach ($reports->toArray() as $report) {
                foreach ($report as $thres) {
                    [$parameterId, $thresholdId] = $thres;
                    $analysis = $analyses[$parameterId];

                    foreach ($analysis as $a) {
                        $rows[] = [
                            'analysis_id' => $a,
                            'threshold_id' => $thresholdId
                        ];
                    }
                }
            }

            DB::table('analysis_threshold')->insert($rows);
            $this->sample->refresh();
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
            ->sampledFrom($from ?? now()->startOfMonth())
            ->sampledUntil($until ?? now())
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
                $query->whereIn('code', $this->getAnalysesByArea($user));
            });
        }

        match ((int) $state) {
            0 => $analyses->where('registered', false),
            1 => $analyses->where('registered', true),
            default => null
        };

        return $analyses->get()->append('isRanged');
    }

    private function insert()
    {
        $this->sample->loadCount('takes');
        $this->sample->load('takes');
        $totalTakes = $this->sample->takes_count;
        $rows = [];
        $this->parameters()
            ->each(function($param) use(&$rows, $totalTakes) {
                $row = [
                    'parameter_id' => $param->parameter_id,
                    'sample_id' => $this->sample->id
                ];

                if ($param->quantity > 1) {
                    for ($i = 1; $i <= $totalTakes; $i++) {
                        $row['index'] = $i;
                        $row['take_id'] = $this->sample->takes->where('sequence', $i)->first()->id;
                        $row['lab_matrix_id'] = $this->sample->matrix->id;
                        $rows[] = $row;
                    }

                    return;
                }

                $row['lab_matrix_id'] = $this->sample->matrix->id;
                $row['index'] = 1;
                $row['take_id'] = $this->sample->takes->first()->id;
                $rows[] = $row;
            });

        DB::table('analyses')->insert($rows);
    }

    private function reports()
    {
        return $this->sample
            ->samplingFormat
            ->entry
            ->reports()
            ->with('thresholds')
            ->get();
    }

    private function parameters()
    {
        return $this->sample
            ->samplingFormat
            ->entry
            ->parameters;
    }

    private function getAnalysesByArea(User $user)
    {
        $analysisAreas = [];

        if ($user->hasRole('fq_analyst')) {
            $analysisAreas[] = 'FQ';
        }

        if ($user->hasRole('aa_analyst')) {
            $analysisAreas[] = 'AA';
        }

        if ($user->hasRole('mb_analyst')) {
            $analysisAreas[] = 'MB';
        }

        if ($user->hasRole('icp_analyst')) {
            $analysisAreas[] = 'ICP';
        }

        if ($user->hasRole('uv_analyst')) {
            $analysisAreas[] = 'UV';
        }

        return $analysisAreas;
    }
}
