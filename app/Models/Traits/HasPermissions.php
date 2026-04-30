<?php

namespace App\Models\Traits;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasPermissions {

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function givePermission(string | Permission $permission)
    {
        $this->permissions()->attach(
            $permission instanceof Permission
            ? $permission->id
            : Permission::where('name', $permission)->firstOrFail()->id
        );
    }
}