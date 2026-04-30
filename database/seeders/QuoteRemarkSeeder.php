<?php

namespace Database\Seeders;

use App\Models\Catalog\QuoteRemark;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuoteRemarkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = file_get_contents(database_path('data/quote_remarks.json'));
        $data =  json_decode($json, true);
        
        foreach ($data as $remark) {
            QuoteRemark::create([
                'code' => $remark['code'],
                'description' => $remark['description']
            ]);
        }
    }
}
