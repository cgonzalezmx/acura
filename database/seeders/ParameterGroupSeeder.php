<?php

namespace Database\Seeders;

use App\Models\Parameters\Group;
use App\Models\Tree\ParameterGroup;
use App\Services\Parameters\GroupService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParameterGroupSeeder extends Seeder
{
    public function __construct(
        private GroupService $service
    )
    {}

    private function groups()
    {
        return [
            [
                'data' => [
                    'name' => 'Grupo 1',
                    'order' => 1,
                    'required_sample_volume' => '1000 mL',
                    'sample_container_id' => 1,
                    'sample_preserver_id' => 1,
                    'label_color_id' => 1
                ],
                'parameters' => [
                    'parámtero 1',
                    'parámtero 2',
                    'parámtero 3',
                    'parámtero 4',
                    'parámtero 5',
                    'parámtero 6',
                    'parámtero 7',
                    'parámtero 8',
                    'parámtero 9',
                    'parámtero 10',
                ]
            ],
            //[
                //'data' => [
                    //'name' => '',
                    //'order' => 0,
                    //'required_sample_volume' => '',
                    //'sample_container_id' => 0,
                    //'sample_preserver_id' => 1
                //],
                //'parameters' => [
                //]
            //]
        ];
    }

    private function register()
    {
        $groups = $this->groups();

        foreach($groups as $g) {
            $group = Group::create(collect($g['data'])->except('matrix')->toArray());
            $query = DB::table('parameters')
                ->select('id')
                ->whereIn('name', $g['parameters']);

            if (isset($g['data']['matrix'])) {
                $query->where('lab_matrix_id', '=', $g['data']['matrix']);
            }

            $parameterIds = $query->get();

            $this->service->sync($group->id, $parameterIds->pluck('id')->toArray());
        }
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->register();
    }
}
