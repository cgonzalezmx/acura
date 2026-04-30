<?php

namespace App\Http\Controllers\Regulatory\Structure;

use App\Http\Controllers\Controller;
use App\Http\Resources\Regulatory\Structure\BundleResource;
use App\Models\Regulatory\Structure\Bundle;
use App\Services\Regulatory\Structure\TreeService;
use Illuminate\Http\Request;

class BundleController extends Controller
{
    public function __construct(
        protected TreeService $tree
    ) {}

    public function show(Bundle $bundle)
    {
        return new BundleResource($bundle);
    }

    public function store(Request $request)
    {
        $bundle = Bundle::create($request->only(['takes', 'price', 'regulation_id']));
        $node = $this->tree->createNodefor($bundle, $request);
        return $node;
    }

    public function update(Bundle $bundle, Request $request)
    {
        $node = $bundle->node;
        $bundle->update($request->only(['takes', 'price']));
        $node->load('nodable');
        $this->tree->updateNode($node, $request);

        return $node->toResource();
    }

    public function package(Bundle $bundle, Request $request)
    {
        $bundle->parameters()->sync($request->bundle);
        return response()->json($bundle->parameters->map(fn($param) => ['id' => $param->id]));
    }

    public function attachParameters(Bundle $bundle, Request $request)
    {
        $parameters = $request->parameters;
        $parametersInBundle = $bundle->parameters()->pluck('parameters.id');
        $additions = collect($parameters)->diff($parametersInBundle);
        $deletions = $parametersInBundle->diff($parameters);

        if ($additions->isNotEmpty()) {
            $additions->each(function($param) use($bundle) {
                $bundle->parameters()->attach($param);
            });
        }

        if ($deletions->isNotEmpty()) {
            $deletions->each(function($param) use($bundle) {
                $bundle->parameters()->detach($param);
            });
        }
    }
}
