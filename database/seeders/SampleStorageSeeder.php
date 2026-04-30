<?php

namespace Database\Seeders;

use App\Models\Catalog\SampleStorage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SampleStorageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $storages = [
            'almacén 1',
            'almacén 2',
            'almacén 3',
        ];

        SampleStorage::create(['id' => 0, 'identifier' => 'Sin almacenador']);

        foreach ($storages as $storage) {
            SampleStorage::create(['identifier' => $storage]);
        }
    }
}
