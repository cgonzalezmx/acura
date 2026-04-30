<?php

namespace Database\Seeders;

use App\Models\Parameter;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ParameterSamplingRemarkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $input = file_get_contents(database_path('data/parameter_sampling_remarks.json'));
        $remarks = collect(json_decode($input));
        $paramNames = $remarks->unique('name')->pluck('name');
        $remarkMap = $remarks->mapWithKeys(function($remark) {
            return ["{$remark->name}:{$remark->matrix}" => $remark];
        });

        $parameters = Parameter::query()->whereIn('name', $paramNames->toArray())->get();
        $parameters->each(function(Parameter $param) use($remarkMap) {
            $remark = $remarkMap["{$param->name}:{$param->lab_matrix_id}"] ?? null;
            if ($remark) {
                $param->samplingRemarks()->sync($remark->remark);
            }
        });
    }
}
