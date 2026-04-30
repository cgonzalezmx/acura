<?php

namespace Database\Seeders;

use App\Models\Catalog\AnalysisArea;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnalysisAreasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $records = [
            ['Área 1', 'A1'],
            ['Área 2', 'A2'],
            ['Área 3', 'A3'],
            ['Área 4', 'A4'],
            ['Área 5', 'A5'],
            ['Área 6', 'A6'],
            ['Área 7', 'A7'],
            ['Área 8', 'A8'],
            ['Área 9', 'A9'],
            ['Área 10', 'A10'],
        ];

        foreach($records as $record) {
            [$name, $code] = $record;
            AnalysisArea::create(['name' => $name, 'code' => $code]);
        }
    }
}
