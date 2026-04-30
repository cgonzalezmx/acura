<?php

namespace App\Http\Controllers;

use App\Services\Samples\SampleSearchService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function __construct(
        private SampleSearchService $searchService
    ){}

    public function index(Request $request)
    {
        $samples = function() use ($request) {
            return $this->searchService->getReportIndex($request->input());
        };

        return inertia('Reports/Index', [
            'samples' => Inertia::optional($samples)
        ]);
    }
}
