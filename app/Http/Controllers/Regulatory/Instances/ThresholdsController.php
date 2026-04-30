<?php

namespace App\Http\Controllers\Regulatory\Instances;

use App\Http\Controllers\Controller;
use App\Models\Regulatory\Instances\Threshold;
use Illuminate\Http\Request;

class ThresholdsController extends Controller
{
    public function update(Threshold $threshold, Request $request)
    {
        $threshold->update($request->only(['min', 'max']));
        return $threshold->toResource();
    }

    public function store(Request $request)
    {
        $threshold = Threshold::create($request->only([
            'regulation_id',
            'regulation_instance_id',
            'parameter_id',
            'min',
            'max'
        ]));
        return $threshold->toResource();
    }
}
