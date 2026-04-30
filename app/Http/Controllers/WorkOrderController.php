<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\Analyses\AnalysisResource;
use App\Models\Catalog\LabMatrix;
use App\Services\Analyses\AnalysisService;
use App\Services\Parameters\ParameterService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WorkOrderController extends Controller
{
    public function __construct(
        private AnalysisService $service,
        private ParameterService $parameterService,
        private UserService $userService
    ){}

    public function index(Request $request)
    {
        $user = $request->user();
        $analyses = null;
        if ($request->hasAny(['from', 'until', 'state'])) {
            $analyses = $this->service->listAnalyses(
                $user,
                from: $request->from,
                until: $request->until,
                state: $request->state,
                parameter: $request->parameter
            );
        }

        $analysisAreas = $this->userService->analysisAreas($user);
        $parameters = $this->parameterService->getByAnalysisArea($analysisAreas, ['name'])->pluck('name');
        return inertia('WorkOrders/Index', [
            'matrices' => LabMatrix::all()->pluck('code'),
            'parameters' => $parameters,
            'analyses' => Inertia::optional(fn() => AnalysisResource::collection($analyses))
        ]);
    }
}
