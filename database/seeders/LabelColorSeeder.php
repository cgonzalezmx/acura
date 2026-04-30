<?php

namespace Database\Seeders;

use App\Models\Catalog\LabelColor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LabelColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colors = [
            'Blanca',
            'Roja',
            'Amarilla',
            'Verde',
            'Naranja'
        ];

        LabelColor::create(['id' => 0, 'color' => 'Sin etiqueta']);

        foreach ($colors as $color) {
            LabelColor::create(['color' => $color]);
        }
    }
}
