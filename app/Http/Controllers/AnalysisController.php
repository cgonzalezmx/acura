<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use Illuminate\Http\Request;

class AnalysisController extends Controller
{
    public function show(Batch $batch)
    {
        return inertia('Analyses/Index');
    }
}
