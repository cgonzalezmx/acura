<?php

namespace App\Services;

use Spatie\Permission\Models\Role;

class RoleService
{
    public function all()
    {
        return Role::all();
    }
}