<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class RoleService
{
    public function getAll()
    {
        return Role::all();
    }

    public function create(array|string $role)
    {

        try {
            return Role::create([
                'name' => $role,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return null;
        }
    }

    public function getRoleByName(string $name)
    {
        try {
            return Role::query()->whereRaw('LOWER(name) = ?', [strtolower($name)])->first();
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return null;
        }
    }

    public function permissions()
    {
        return Role::getPermissions();
    }
}
