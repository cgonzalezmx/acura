<?php

namespace App\Services\Regulatory\Structure;

use App\Http\Resources\Regulatory\Structure\NodeResource;
use App\Models\Regulatory\Instances\Node as InstanceNode;
use App\Models\Regulatory\Structure\Bundle;
use App\Models\Regulatory\Structure\Node as StructureNode;
use App\Models\Regulatory\Structure\Regulation;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class TreeService
{
    public function getRootNodes()
    {
        return StructureNode::where('parent_id', null)
            ->get()
            ->toResourceCollection();
    }

    public function loadTree($structureLeaves, $instanceLeaves)
    {
        $structureNodes = StructureNode::whereIn('id', $structureLeaves)->get();
        $instanceNodes = InstanceNode::whereIn('id', $instanceLeaves)->get();
        $this->loadStructureMorphs($structureNodes);
        $branches = $this->buildTree($structureNodes, $instanceNodes);
        return NodeResource::collection($branches);
    }

    public function createNodefor(Bundle | Regulation $nodable, Request $request)
    {
        $node = StructureNode::create($this->nodeAttributes($request));
        $nodable->node()->save($node);
        $node->loadMorph('nodable', [
            Regulation::class => ['parameters', 'labMatrix'],
            Bundle::class => ['parameters']
        ]);

        return new NodeResource($node);
    }

    public function updateNode(StructureNode $node, Request $request)
    {
        $node->update([
            ...$request->only('name'),
            'alias' => $request->filled('alias') ? $request->alias: ''
        ]);
    }

    private function buildTree(Collection $structureBranches, Collection $instanceBranches)
    {
        $structureMap = $structureBranches->keyBy('id');
        $reconstructedStructures = collect();
        $reconstructedInstances = $this->buildInstanceTree($instanceBranches);
        $reconstructedInstancesMap = $reconstructedInstances->keyBy('regulation_id');

        foreach($structureBranches as $branch) {
            $parentId = $branch->parent_id;

            if($parentId && $structureMap->has($parentId)) {
                $parent = $structureMap->get($parentId);
                $children = $parent->children;
                $this->loadStructureMorphs($children);

                if ($branch->type === 'regulation') {
                    $regulation = $branch->nodable;
                    $expandedInstance = $reconstructedInstancesMap->get($regulation->id);
                    $instances = $regulation->instances;

                    $existingInstanceKey = $instances->search(function($instance) use($expandedInstance) {
                        return $instance->id === $expandedInstance->id;
                    });

                    $instances->put($existingInstanceKey, $expandedInstance);
                    $regulation->setRelation('instances', $instances);
                }

                $childrenMap = $parent->children->keyBy('id');
                $childrenMap->put($branch->id, $branch);
                $parent->setRelation('children', $childrenMap->values()->sortBy('id'));
            }
            else {
                $reconstructedStructures->push($branch);
            }
        }

        return $reconstructedStructures->sortBy('id');
    }

    private function buildInstanceTree(Collection $branches)
    {
        $map = $branches->keyBy('id');
        $reconstrudted = collect();

        foreach($branches as $branch) {
            $parentId = $branch->parent_id;

            if($parentId && $map->has($parentId)) {
                $parent = $map->get($parentId);
                $childrenMap = $parent->children->keyBy('id');
                $childrenMap->put($branch->id, $branch);
                $parent->setRelation('children', $childrenMap->values()->sortBy('id'));
            }
            else {
                $reconstrudted->push($branch);
            }
        }

        return $reconstrudted->sortBy('id');
    }

    private function nodeAttributes(Request $request): array
    {
        $attributes = ['name', 'type', 'parent_id'];
        
        if ($request->filled('alias')) {
            $attributes[] = 'alias';
        }

        return $request->only($attributes);
    }

    private function loadStructureMorphs(EloquentCollection $nodes)
    {
        $nodes->loadMorph('nodable', [
            Regulation::class => ['parameters', 'labMatrix'],
            Bundle::class => ['parameters']
        ]);
    }
}