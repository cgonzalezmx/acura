<?php

namespace App\Http\Controllers\Parameters;

use App\Http\Controllers\Controller;
use App\Services\Parameters\GroupService;
use Illuminate\Http\Request;

class SyncParameterToGroupContoller extends Controller
{
    public function __invoke(int $groupId, Request $request, GroupService $service)
    {
        $service->sync($groupId, $request->id_payload);
    }
}
