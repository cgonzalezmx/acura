<?php

namespace App\Http\Controllers\Samples;

use App\Http\Controllers\Controller;
use App\Http\Requests\Samples\StoreSampleRequest;
use App\Models\Samples\Sample;
use App\Services\Samples\SampleIndex;
use App\Services\Samples\SampleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SampleController extends Controller
{
    public function __construct(
        private SampleIndex $indexService,
        private SampleService $service
    )
    {}

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
}
