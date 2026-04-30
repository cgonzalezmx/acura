<?php

namespace App\Http\Controllers\Regulatory\Structure;

use App\Http\Controllers\Controller;
use App\Models\Regulatory\Structure\Bundle;
use App\Models\Regulatory\Structure\Regulation;
use App\Models\Regulatory\Structure\Node;
use Illuminate\Http\Request;
use App\Services\Regulatory\Structure\TreeService;

class NodeController extends Controller
{
    public function __construct(
        protected TreeService $tree
    )
    {}

    public function root()
    {
        return $this->tree->getRootNodes();
    }

    public function workspace()
    {
        return inertia('Regulations/Index', [
            'nodes' => fn() => $this->tree->getRootNodes()
        ]);
    }

    public function childrenForView(Node $node)
    {
        $node->children->loadMorph('nodable', [
            Regulation::class => ['labMatrix'],
            Bundle::class => ['parameters']
        ]);
        return $node->children->toResourceCollection();
    }

    public function childrenForEdit(Node $node) {
        $children = $node->children;
        $children->loadMorph('nodable', [
            Regulation::class => ['parameters', 'labMatrix'],
            Bundle::class => ['parameters']
        ]);

        return $children->toResourceCollection();
    }

    public function store(Request $request)
    {
        $inputs = ['name', 'type', 'parent_id'];

        if ($request->filled('alias')) {
            $inputs[] = 'alias';
        }

        $node = Node::create($request->only($inputs));
        return $node->toResource();
    }

    public function update(Node $node, Request $request)
    {
        $this->tree->updateNode($node, $request);
        return $node->toResource();
    }

    public function destroy(Node $node)
    {
        $parent = Node::find($node->parent_id);
        $node->delete();

        if (isset($parent->children)) {
            return $parent->children->toResourceCollection();
        }

        return response()->json([]);
    }
}