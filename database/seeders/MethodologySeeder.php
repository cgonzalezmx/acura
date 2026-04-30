<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Catalog\Methodology;

class MethodologySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = file_get_contents(database_path('data/methodologies.json'));
        $data = json_decode($json, true);

        Methodology::create(['id' => 0, 'name' => 'N.A.']);

        foreach ($data as $method) {
            Methodology::create(['name' => $method]);
        }
    }
}
