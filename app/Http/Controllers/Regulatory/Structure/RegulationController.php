<?php

namespace App\Http\Controllers\Regulatory\Structure;

use App\Http\Controllers\Controller;
use App\Http\Resources\Parameters\RegulationResource as ParameterResource;
use App\Http\Resources\Regulatory\Structure\RegulationResource;
use App\Models\Parameter;
use App\Models\Regulatory\Structure\Regulation;
use App\Services\Regulatory\Structure\TreeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RegulationController extends Controller
{
    public function __construct(
        protected TreeService $tree
    ) {}

    public function show(Regulation $regulation)
    {
        $regulation->load(['labMatrix:id,name', 'parameters:id,name,analysis_area_id', 'parameters.analysisArea']);
        return new RegulationResource($regulation);
    }

    public function addParameter(Regulation $regulation, Request $request)
    {
        Validator::make($request->all(), [
                'parameter_id' => [
                    'required',
                    Rule::unique('regulation_parameter', 'parameter_id')->where('regulation_id', $regulation->id)
                ]
            ],
            [
                'parameter_id' => 'El parámetro ya está registrado'
            ]
        )->validate();
        $parameter = Parameter::find($request->parameter_id);
        $regulation->parameters()->attach($parameter->id);
        return new ParameterResource($parameter);
    }

    public function store(Request $request)
    {
        $regulation = Regulation::create($request->only('lab_matrix_id'));
        return $this->tree->createNodefor($regulation, $request);
    }

    public function saveObservation(Regulation $regulation, Request $request)
    {
        $regulation->observation = $request->text;
        $regulation->save();
    }
}
