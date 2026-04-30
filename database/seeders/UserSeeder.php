<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->userRoles();
    }

    private function userRoles()
    {
        DB::table('users')->insert([
            'name' => 'Muestreador',
            'alias' => 'SAMPLER',
            'password' => 'sampler123@'
        ]);
        $userRoles = [
            ['ADMIN', 'admin'],
            ['SAMPLER', 'sampler'],
        ];

        foreach ($userRoles as $ur) {
            [$alias, $roles] = $ur;
            User::where('alias', $alias)->first()->assignRole($roles);
        }
    }
}
