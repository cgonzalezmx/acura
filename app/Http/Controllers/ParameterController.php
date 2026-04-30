<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\ParameterResource;
use App\Models\Catalog\AnalysisArea;
use App\Models\Catalog\LabelColor;
use App\Models\Catalog\LabMatrix;
use App\Models\Catalog\MeasurementUnit;
use App\Models\Catalog\Methodology;
use App\Models\Catalog\QuoteRemark;
use App\Models\Catalog\SampleContainer;
use App\Models\Catalog\SamplePreserver;
use App\Models\Catalog\SampleStorage;
use App\Models\Catalog\SamplingRemark;
use App\Models\Parameter;
use App\Models\Parameters\Group;
use App\Services\Quotes\ParameterService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ParameterController extends Controller
{
    protected array $attributes = [
        'name',
        'price',
        'unit_volume',
        'group_volume',
        'parameter_category_id',
        'lab_matrix_id',
        'analysis_area_id',
        'methodology_id',
        'measurement_unit_id',
        'sample_container_id',
        'sample_preserver_id',
        'sample_storage_id',
        'label_color_id'
    ];
    protected string $modelClass = Parameter::class;

    public function index()
    {
        $quoteRemarks = QuoteRemark::select(['id', 'code', 'description'])
            ->get()
            ->sortBy('code', SORT_NATURAL);

        $samplingRemarks = SamplingRemark::select(['id', 'code', 'description'])
            ->get();

        return inertia('Parameters/Index', [
            'parameters' => fn() => ParameterResource::collection(Parameter::with([
                'methodology',
                'analysisArea',
                'labMatrix',
                'labelColor',
                'sampleStorage',
                'samplePreserver',
                'sampleContainer',
                'measurementUnit',
                'quoteRemarks:id',
                'samplingRemarks:id'
            ])->get()),
            'labMatrices' => fn() => LabMatrix::select('id', 'code')->get(),
            'methodologies' => fn() => Methodology::select('id', 'name')->get(),
            'sampleContainers' => fn() => SampleContainer::select('id', 'name')->get(),
            'analysisAreas' => fn() => AnalysisArea::select('id', 'name', 'code')->get(),
            'labelColors' => fn() => LabelColor::select('id', 'color')->get(),
            'samplePreservers' => fn() => SamplePreserver::select('id', 'name')->get(),
            'measurementUnits' => fn() => MeasurementUnit::select('id', 'unit')->get(),
            'sampleStorages' => fn() => SampleStorage::select('id', 'identifier')->get(),
            'quoteRemarks' => fn() => $quoteRemarks,
            'samplingRemarks' => fn() => $samplingRemarks,
            'groups' => Inertia::optional(fn() => Group::all())
        ]);
    }

    public function store(Request $request, ParameterService $service)
    {
        $request->validate([
            'name' => 'required|string'
        ]);

        $service->create($request);

        return back();
    }

    public function update(Request $request, string $id, ParameterService $service)
    {
        $request->validate(['name' => 'required|string']);

        $parameter = Parameter::findOrFail($id);
        $service->update($parameter, $request);
        return back();
    }

    public function destroy(string $id)
    {
        $parameter = Parameter::findOrFail($id);
        $parameter->delete();
        return back();
    }
}
