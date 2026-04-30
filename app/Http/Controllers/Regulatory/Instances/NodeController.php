<?php

namespace App\Http\Controllers\Regulatory\Instances;

use App\Http\Controllers\Controller;
use App\Models\Regulatory\Instances\Node;
use Illuminate\Http\Request;

class NodeController extends Controller
{
    public function store(Request $request)
    {
        $inputs = ['name', 'type', 'parent_id', 'regulation_id'];

        if ($request->filled('alias')) {
            $inputs[] = 'alias';
        }

        $node = Node::create($request->only($inputs));
        return $node->toResource();
    }

    public function update(Node $node, Request $request)
    {
        $inputs = ['name'];

        if ($request->filled('alias')) {
            $inputs[] = 'alias';
        }

        $node->update($request->only($inputs));
        return $node->toResource();
    }

    public function children(Node $node)
    {
        return $node->children->toresourceCollection();
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
