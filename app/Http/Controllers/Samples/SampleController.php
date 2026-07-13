<?php

namespace App\Http\Controllers\Samples;

use App\Http\Controllers\Controller;
use App\Http\Requests\Samples\StoreSampleRequest;
use App\Http\Resources\Samples\ResultResource;
use App\Models\Samples\Sample;
use App\Services\Samples\SampleIndex;
use App\Services\Samples\SampleService;
use Illuminate\Http\Request;

class SampleController extends Controller
{
    public function __construct(
        private SampleIndex $indexService,
        private SampleService $service
    ){}

    public function index()
    {
        return $this->indexService->view();
    }

    public function store(StoreSampleRequest $request)
    {
        $validated = $request->validated();
        $this->service->store(...$validated);
    }

    public function update(Sample $sample, StoreSampleRequest $request)
    {
        $validated = $request->validated();
        $this->service->update($sample, $validated);
    }

    public function search(Request $request)
    {
        $samples = Sample::select(['id', 'identifier'])
            ->whereLike('identifier', "%{$request->str('term')}%")
            ->get();
        return response()->json($samples);
    }

    public function show(int $sampleId)
    {
        $relations = $this->service->getRelations();
        $sample = Sample::with($relations)
            ->withCount(['reports'])
            ->find($sampleId);
        $sample->append('client');
        $sample->analyses->append('smallest_max_threshold');
        return inertia('Samples/Results', ['sample' => new ResultResource($sample)]);
    }
}
