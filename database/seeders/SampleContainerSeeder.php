<?php

namespace Database\Seeders;

use App\Models\Catalog\SampleContainer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SampleContainerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $containers = [
            'Frasco 1',
        ];

        foreach ($containers as $container) {
            SampleContainer::create(['name' => $container]);
        }
    }
}
