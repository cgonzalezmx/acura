<?php

namespace Database\Seeders;

use App\Models\Parameter;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParameterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('parameters')->insert($this->json());
        DB::table('parameters')
            ->whereIn('name', [
                'parametro 1',
                'parametro 2',
                'parametro 4',
                'parametro 5',
            ])
            ->update(['multiple' => 1]);
    }

    private function json()
    {
        $json = file_get_contents(database_path('data/parameters.json'));
        return collect(json_decode($json, true))
            ->map(function($item) {
                $uncertainties = $this->uncertaintyRange($item['uncertainty']);
                $isRanged = isset($uncertainties['uncertainty_low_range']);
                return [
                    'name' => $item['name'],
                    'measurement_unit_id' => $item['measurement_unit_id'],
                    'lab_matrix_id' => $item['lab_matrix_id'],
                    'methodology_id' => $item['methodology_id'],
                    'price' => $item['price'],
                    'unit_volume' => $item['unit_volume'],
                    'group_volume' => $item['group_volume'],
                    'sample_container_id' => $item['sample_container_id'],
                    'label_color_id' => $item['label_color_id'],
                    'validity' => $item['validity'],
                    'sample_preserver_id' => $item['sample_preserver_id'],
                    'sample_storage_id' => $item['sample_storage_id'],
                    'analysis_area_id' => $item['analysis_area_id'],
                    ...$this->uncertaintyRange($item['uncertainty']),
                    'quantification_low_range' => $isRanged ? $item['quantification'] : null,
                    'quantification_mid_range' => $isRanged ? null : $item['quantification'],
                    'quantification_high_range' => $isRanged ? $item['quantification'] : null,
                ];
            })
            ->toArray();
    }

    private function uncertaintyRange(string | null $uncertainty)
    {
        $values = explode('-', $uncertainty);
        $lowRange = null;
        $highRange = null;
        $midRange = null;

        if (count($values) > 1) {
            [$lowRange, $highRange] = $values;
        }
        else {
            $midRange = $uncertainty;
        }

        return [
            'uncertainty_low_range' => $lowRange,
            'uncertainty_mid_range' => $midRange,
            'uncertainty_high_range' => $highRange
        ];
    }
}
