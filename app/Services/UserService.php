<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService
{
    public function setSignaturePath(User $user, string $signaturePath)
    {
        $user->signature_path = $signaturePath;
    }

    public function upsertSignature(User $user, UploadedFile $signature)
    {
        if (isset($user->signature_path)) {
            Storage::disk('public')->delete($user->signature_path);
        }

        $user->signature_path = $signature->store('signatures', 'public');
    }

    public function upsert(User $user, mixed $data)
    {
        DB::transaction(function() use($user, $data) {
            $user->name = $data['name'];
            $user->alias = $data['alias'];

            if (!isset($user->id)) {
                $user->password = Hash::make($data['password']);
            }

            if (isset($data['signature'])) {
                $this->upsertSignature($user, $data['signature']);
            }
            $user->save();
            $user->roles()->sync($data['roles']);
        });
    }

    public function listSamplers()
    {
        return User::whereRelation('roles', 'label', ['Muestreador', 'Supervisor de muestreo'])
            ->select(['id', 'name'])
            ->get();
    }

    public function analysisAreas(User $user)
    {
        if ($user->hasRole('admin')) {
            return ['A1', 'A2'];
        }

        $analysisAreas = [];

        if ($user->hasRole('a1_analyst')) {
            $analysisAreas[] = 'A1';
        }

        if ($user->hasRole('a2_analyst')) {
            $analysisAreas[] = 'A2';
        }

        return $analysisAreas;
    }
}
