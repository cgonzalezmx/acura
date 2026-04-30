<?php

namespace App\Http\Controllers\Parameters;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupEditionRequest;
use App\Models\Parameters\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function store(GroupEditionRequest $request)
    {
        $validated = $request->validated();
        Group::create($validated);
    }

    public function update(string $id, GroupEditionRequest $request)
    {
        $group = Group::find($id);
        $validated = $request->validated();
        $group->fill($validated);
        $group->save();
        return redirect()->route('parameters.index');
    }

    public function show(Group $parameterGroup)
    {
        return response()->json($parameterGroup->parameters);
    }

    public function destroy(string $id) {
        $group = Group::find($id);
        $group->delete();
        return redirect()->route('parameters.index');
    }
}
