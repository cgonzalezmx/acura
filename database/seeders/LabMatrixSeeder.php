<?php

namespace Database\Seeders;

use App\Models\Catalog\LabMatrix;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LabMatrixSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $matrices = [
            ['Matriz 1', 'M1'],
        ];


        foreach ($matrices as $matrix) {
            [$name, $code] = $matrix;
            LabMatrix::create(['name' => $name, 'code' => $code]);
        }
    }
}
