<?php

namespace Database\Seeders;

use App\Models\Catalog\SamplePreserver;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SamplePreserverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $preservers = [
            'preservador 1'
        ];

        foreach ($preservers as $preserver) {
            SamplePreserver::create(['name' => $preserver]);
        }
    }
}
