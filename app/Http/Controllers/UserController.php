<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\RoleService;
use App\Services\UserService;

class UserController extends Controller
{
    public function index(RoleService $roles) {
        $activeUsers = User::with('roles:id,label')->get();
        return inertia('Users/Index', [
            'activeUsers' => UserResource::collection($activeUsers),
            'activeRoles' => $roles->all()
        ]);
    }

    public function store(UserRequest $request, UserService $service)
    {
        $validated = $request->validated();
        $user = new User();
        $service->upsert($user, $validated);
    }

    public function update(User $user, UserRequest $request, UserService $service)
    {
        $validated = $request->validated();
        $service->upsert($user, $validated);
    }

    public function destroy(User $user)
    {
        $user->delete();
    }

    public function samplers(UserService $service)
    {
        return $service->listSamplers();
    }
}
