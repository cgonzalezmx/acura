<?php

namespace Database\Seeders;

use App\Models\Catalog\MeasurementUnit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MeasurementUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = file_get_contents(database_path('data/measurement_units.json'));
        $data = json_decode($json, true);

        MeasurementUnit::create(['id' => 0, 'unit' => 'N.A.']);

        foreach ($data as $unit) {
            MeasurementUnit::create(['unit' => $unit]);
        }
    }
}
