<?php

namespace App\Services\Regulatory\Instances;

use App\Models\Regulatory\Instances\Node;
use App\Models\Regulatory\Structure\Regulation;

class InstancesService
{
    public function createNode(array $data, Regulation $regulation, Node $parent = null)
    {
        $node = new Node();
        $node->name = $data['name'];
        $node->type = $data['type'] ?? 'node';
        $node->alias = $data['alias'] ?? '';
        $node->regulation()->associate($regulation);

        if (isset($parent)) {
            $node->parentNode()->associate($parent);
        }

        $node->save();
        return $node;
    }

    public function getLeafByPath(array $path, int $regulationId)
    {
        $current = null;

        foreach($path as $name) {
            if (!$current) {
                $current = Node::query()
                    ->where('regulation_id', '=', $regulationId)
                    ->where('name', '=', $name)
                    ->firstOrFail();
            }
            else {
                $current = $current->children()
                    ->where('regulation_id', '=', $regulationId)
                    ->where('name', '=', $name)
                    ->firstOrFail();
            }
        }

        return $current;
    }
}