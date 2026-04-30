<?php

namespace Database\Seeders;

use App\Models\Parameter;
use App\Models\Regulatory\Instances\Node as InstanceNode;
use App\Models\Regulatory\Structure\Node as StructureNode;
use App\Models\Regulatory\Structure\Regulation;
use App\Services\Regulatory\Instances\InstancesService;
use App\Services\Regulatory\Structure\BundleService;
use App\Services\Regulatory\Structure\RegulationService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TreeSeeder extends Seeder
{
    public function __construct(
        private Regulation $regulation,
        private RegulationService $regulationService,
        private BundleService $bundleService,
        private InstancesService $instanceService
    ){}

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = file_get_contents(database_path('data/tree.json'));
        $json = json_decode($json, true);

        DB::transaction(function() use($json) {
            $this->buildTree($json);
        });
    }

    private function buildTree(array $branches, StructureNode $parentNode = null)
    {
        foreach($branches as $node) {
            $structureNode = new StructureNode();
            $type = $node['type'] ?? 'node';
            $structureNode->name = $node['name'];
            $structureNode->type = $type;
            $structureNode->alias = $node['alias'] ?? '';

            if(isset($parentNode)) {
                $structureNode->parentNode()->associate($parentNode);
            }

            $structureNode->save();

            if ($type === 'regulation') {
                $regulation = $this->regulationService->createRegulation($node);
                $this->regulation = $regulation;
                $regulation->node()->save($structureNode);

                if (isset($node['instances'])) {
                    $this->buildInstanceTree($node['instances'], $regulation);
                }

                if (isset($node['parameters'])) {
                    $parametersWithThresholds = $this->gatherParameters($node['parameters'], $node['lab_matrix_id']);
                    $paramIds = collect($parametersWithThresholds)->pluck('id');
                    $regulation->parameters()->attach($paramIds);
                    $thresholds = $this->buildThresholds($parametersWithThresholds, $regulation->id);
                    DB::table('regulatory_thresholds')->insert($thresholds);
                }
            }

            if ($type === 'bundle') {
                $bundle = $this->bundleService->createBundle($node);
                $this->regulation->bundles()->save($bundle);
                $bundle->node()->save($structureNode);
            }

            $children = $node['children'] ?? null;

            if (isset($children)) {
                $this->buildTree($children, $structureNode);
            }
        }
    }

    private function buildInstanceTree(array $branches, Regulation $regulation, InstanceNode $parent = null)
    {
        foreach($branches as $node) {
            $instanceNode = $this->instanceService->createNode($node, $regulation, $parent);
            $children = $node['children'] ?? null;

            if(isset($children)) {
                $this->buildInstanceTree($children, $regulation, $instanceNode);
            }
        }
    }

    private function gatherParameters(array $list, int $labMatrixId)
    {
        $parameterList = [];

        foreach ($list as $key => $item) {
            [$name, $analysisAreaId] = explode(':', $key);
            $param = Parameter::where('name', '=', $name)
                ->where('lab_matrix_id', '=', $labMatrixId)
                ->where('analysis_area_id', '=', $analysisAreaId)
                ->first();

            if ($param) {
                $parameterList[] = [
                    'id' => $param->id,
                    'thresholds' => $item['thresholds']
                ];
            }
        }
        return $parameterList;
    }

    private function buildThresholds(array $parametersWithThresholds, int $regulationId)
    {
        $thresholds = [];

        foreach($parametersWithThresholds as $parameter) {
            foreach($parameter['thresholds'] as $path => $values) {
                $regulationInstanceId = $this->instanceService->getLeafByPath(explode('/', $path), $regulationId)->id;

                [$min, $max] = $values;
                $thresholds[] = [
                    'regulation_id' => $regulationId,
                    'regulation_instance_id' => $regulationInstanceId,
                    'parameter_id' => $parameter['id'],
                    'min' => $min,
                    'max' => $max
                ];
            }
        }

        return $thresholds;
    }
}
