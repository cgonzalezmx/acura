<?php

namespace Database\Seeders;

use App\Models\Parameter;
use App\Models\Regulatory\Structure\Bundle;
use App\Models\Regulatory\Structure\Node;
use App\Services\Regulatory\Structure\StructureService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BundleSeeder extends Seeder
{
    public function __construct(
        private StructureService $structure
    )
    {}

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = file_get_contents(database_path('data/bundles.json'));
        $json = json_decode($json, true);
        $bundles = $this->prepareBundles($json);

        DB::transaction(function() use($bundles) {
            DB::table('bundle_parameter')->insert($bundles->toArray());
        });
    }

    private function prepareBundles(array $regulations)
    {
        $preparedBundles = collect();
        foreach ($regulations as $regulation => $bundleSchemes) {
            $regulationNodes = Node::where('name', '=', $regulation)->get();

            $regulationNodes->each(function($node) use($bundleSchemes, &$preparedBundles) {
                $parameterMap = [];
                foreach ($bundleSchemes as $scheme) {
                    //$bundleNode = $this->structure
                        //->getLeafByPath(explode('/', $scheme['path']), $node, $scheme['alias'] ?? '');
                    //$mapped = $this->mapParametersToBundle($scheme['items'], $bundleNode->nodable);
                    //$parameterMap = [ ...$parameterMap, ...$mapped ];
                    $parameterMap = [ ...$parameterMap, ...$this->getMapFromScheme($scheme, $node)];
                }

                $preparedBundles = $preparedBundles->merge($parameterMap);
            });
        }

        return $preparedBundles;
    }

    private function getMapFromScheme(array $scheme, Node $node): array
    {
        $mapped = [];

        foreach ($scheme['paths'] as $path) {
            $bundle = $this->structure
                ->getLeafByPath(explode('/', $path), node: $node, alias: $scheme['alias'] ?? '')
                ->nodable;
            $map = $this->mapParametersToBundle($scheme['items'], $bundle);
            $mapped = [...$mapped, ...$map];
        }

        return $mapped;
    }

    private function mapParametersToBundle(array $items, Bundle $bundle)
    {
        $parameterMap = [];

        foreach ($items as $item) {
            $parameters = $bundle
                ->regulation
                ->parameters()
                ->whereIn('name', $item['parameters'])
                ->where('analysis_area_id', '=', $item['analysis_area_id'])
                ->get()
                ->map(fn(Parameter $parameter) => [
                    'parameter_id' => $parameter->id,
                    'bundle_id' => $bundle->id
                ]);
            
            array_push($parameterMap, ...$parameters);
        }

        return $parameterMap;
    }
}
