<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminSeeder::class,
            UserSeeder::class,
            ClientSeeder::class,
            ClientContactSeeder::class,
            ClientSamplingSiteSeeder::class,
            AnalysisAreasSeeder::class,
            LabMatrixSeeder::class,
            MethodologySeeder::class,
            QuoteRemarkSeeder::class,
            SampleContainerSeeder::class,
            SamplePreserverSeeder::class,
            SamplingRemarkSeeder::class,
            MeasurementUnitSeeder::class,
            LabelColorSeeder::class,
            SampleStorageSeeder::class,
            ParameterSeeder::class,
            ParameterSamplingRemarkSeeder::class,
            TreeSeeder::class,
            BundleSeeder::class,
            ParameterGroupSeeder::class
        ]);
    }
}
