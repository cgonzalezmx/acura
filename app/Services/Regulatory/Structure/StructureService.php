<?php

namespace App\Services\Regulatory\Structure;

use App\Models\Regulatory\Structure\Node;

class StructureService
{
    public function getLeafByPath(array $path, Node $node = null, string $alias = '')
    {
        $current = $node ? $node : null;
        $pathLength = count($path);

        foreach($path as $index => $name) {
            $query = !$current ? Node::query() : $current->children();
            $query->where('name', '=', $name);

            if ($index + 1 === $pathLength) {
                $query->where('alias', '=', $alias);
            }

            $current = $query->firstOrFail();
        }

        return $current;
    }
}