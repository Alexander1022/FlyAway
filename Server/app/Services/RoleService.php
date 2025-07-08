<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RoleService
{
    public function getAll(): Collection
    {
        return Role::all();
    }

    public function getById(int $id): ?Role
    {
        try {
            return Role::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'No such location found'], 404);
        }
    }

    public function update(Role $role, array $data)
    {
        $role->update($data);
        return $role;
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return response()->json(['message' => 'Role deleted successfully.'], 200);
    }
}
