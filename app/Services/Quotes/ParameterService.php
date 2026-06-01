<?php

namespace App\Services\Quotes;

use App\Models\Parameter;
use App\Models\Quotes\Quote;
use App\Models\Quotes\Parameter as QuoteParameter;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ParameterService
{
    protected array $modelAttributes = [
        'name',
        'price',
        'unit_volume',
        'group_volume',
        'lab_matrix_id',
        'analysis_area_id',
        'methodology_id',
        'measurement_unit_id',
        'sample_container_id',
        'sample_preserver_id',
        'sample_storage_id',
        'label_color_id',
        'multiple'
    ];

    public function create(Request $request)
    {
        $quoteRemarks = collect($request->quote_remarks)
            ->toArray();
        $samplingRemarks = collect($request->sampling_remarks);
        $parameter = Parameter::create($request->only($this->modelAttributes));
        $parameter->quoteRemarks()->attach($quoteRemarks);
        $parameter->samplingRemarks()->attach($samplingRemarks);
    }

    public function update(Parameter $parameter, Request $request)
    {
        collect($request->only($this->modelAttributes))
            ->each(function($val, $attr) use($parameter) {
                $parameter->setAttribute($attr, $val);
            });

        $parameter->save();
        $parameter->quoteRemarks()->sync($request->quote_remarks);
        $parameter->samplingRemarks()->sync($request->sampling_remarks);
    }

    public function withSystemInfo(Quote $quote): Collection
    {
        return $quote
            ->parameters()
            ->with([
                'systemInfo:id,name,methodology_id',
                'systemInfo.methodology:id,name',
                'systemInfo.quoteRemarks:id,code,description',
                'entry' => fn($query) => $query
                    ->select('id')
                    ->selectRaw('ROW_NUMBER() OVER (ORDER BY id) AS number')
            ])
            ->select(['id', 'parameter_id', 'quote_entry_id'])
            ->get()
            ->map(function(QuoteParameter $param) {
                return [
                    'name' => $param->systemInfo->name,
                    'methodology' => $param->systemInfo->methodology->name,
                    'entry_number' => $param->entry->number,
                    'quote_remarks' => $param->systemInfo->quoteRemarks->map(function($remark) {
                        return [
                            'code' => $remark->code,
                            'description' => $remark->description
                        ];
                    })->toArray()
                ];
            })
            ->sortBy('name');
    }

    public function arrange(Collection $parameters): Collection
    {
        return $parameters
            ->groupBy(fn($param) => "{$param['name']}:{$param['methodology']}")
            ->map(function(Collection $params) {
                $inEntries = $params->pluck('entry_number')->unique()->sort()->implode(',');
                $meaningful = $params->unique('name')->first();

                return [
                    'name' => $meaningful['name'],
                    'methodology' => $meaningful['methodology'],
                    'in_entries' => $inEntries,
                    'quote_remarks' => collect($meaningful['quote_remarks'])->pluck('code')
                ];
            })
            ->sortBy('in_entries');
    }

    public function groupByInEntries(Collection $parameters)
    {
        return $parameters
            ->map(function(Collection $params) {
                return $params
                    ->sortBy([
                        ['methodology', 'asc'],
                        ['name', 'asc']
                    ])
                    ->groupBy('in_entries')
                    ->sortKeys();
            });
    }

    public function shouldSpliceFirstChunk(int $totalEntries, bool $hasPriceAdjustment): bool
    {
        return
            ($totalEntries >= 3 && (($totalEntries - 3) % 10) < 8);
    }

    public function spliceFirstChunk(Collection $parameters, int $totalEntries): Collection
    {
        $splicedQuantity = 8;

        $cycleOffset = ($totalEntries - 3) % 10;

        if ($cycleOffset === 0 && $totalEntries > 3) {
            $splicedQuantity = 0;
        }

        if ($cycleOffset > 0) {
            $splicedQuantity += 3 * ($cycleOffset);
        }

        return $parameters->splice(40 - $splicedQuantity);
    }
}
