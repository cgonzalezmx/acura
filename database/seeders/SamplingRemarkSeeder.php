<?php

namespace Database\Seeders;

use App\Models\Catalog\SamplingRemark;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SamplingRemarkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = file_get_contents(database_path('data/sampling_remarks.json'));
        $json = json_decode($file, associative: true);

        foreach ($json as $remark) {
            SamplingRemark::create([
                'code' => $remark['code'],
                'description' => $remark['description']
            ]);
        }
    }
}
