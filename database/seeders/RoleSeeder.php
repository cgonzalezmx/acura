<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->permissions();
        $this->roles();
    }

    private function permissions()
    {
        $permissions = [
            ['name' => 'quotes.view'],
            ['name' => 'quotes.create'],
            ['name' => 'quotes.edit'],
            ['name' => 'quotes.authorize'],
            ['name' => 'quotes.delete'],
            ['name' => 'sampling_formats.view'],
            ['name' => 'sampling_formats.create'],
            ['name' => 'sampling_formats.delete'],
            ['name' => 'samples.view'],
            ['name' => 'samples.create'],
            ['name' => 'samples.rename'],
            ['name' => 'samples.edit'],
            ['name' => 'samples.delete'],
            ['name' => 'samples.verify'],
            ['name' => 'takes.view'],
            ['name' => 'takes.edit'],
            ['name' => 'work_orders.view'],
            ['name' => 'work_orders.assign'],
            ['name' => 'work_orders.create'],
            ['name' => 'work_orders.edit'],
            ['name' => 'work_orders.delete'],
            ['name' => 'analyses.view.a1'],
            ['name' => 'analyses.view.a2'],
            ['name' => 'analyses.save'],
            ['name' => 'analyses.authorize'],
            ['name' => 'analyses.delete'],
            ['name' => 'analyses.edit'],
            ['name' => 'reports.view'],
            ['name' => 'reports.edit'],
            ['name' => 'reports.authorize'],
            ['name' => 'reports.cancel'],
            ['name' => 'reports.release'],
            ['name' => 'parameters.view'],
            ['name' => 'parameters.create'],
            ['name' => 'parameters.edit'],
            ['name' => 'parameters.delete'],
            ['name' => 'catalog.manage'],
            ['name' => 'users.manage'],
            ['name' => 'clients.manage'],
            ['name' => 'regulations.view'],
            ['name' => 'regulations.create'],
            ['name' => 'regulations.edit'],
            ['name' => 'regulations.delete'],
            ['name' => 'company_data.manage']
        ];

        foreach ($permissions as $p) {
            Permission::create($p);
        }
    }

    private function roles()
    {
        $roles = [
            [
                'name' => 'sampler',
                'label' => 'Muestreador',
                'permissions' => [
                    'sampling_formats.view',
                    'samples.view',
                    'samples.create',
                    'samples.edit',
                    'samples.delete',
                    'takes.edit'
                ]
            ],
            [
                'name' => 'a1_analyst',
                'label' => 'Analista a1',
                'permissions' => [
                    'work_orders.view',
                    'work_orders.create',
                    'analyses.view.a1'
                ]
            ],
            [
                'name' => 'a2_analyst',
                'label' => 'Analista a2',
                'permissions' => [
                    'work_orders.view',
                    'work_orders.create',
                    'analyses.view.a2'
                ]
            ],
            [
                'name' => 'admin',
                'label' => 'Administrador'
            ]
        ];

        foreach ($roles as $r) {
            $role = Role::create(['name' => $r['name'], 'label' => $r['label']]);
            $permissions = $r['permissions'] ?? null;
            if (!isset($permissions) || $permissions === 'all') {
                continue;
            }
            $role->givePermissionTo($permissions);
        }
    }
}
