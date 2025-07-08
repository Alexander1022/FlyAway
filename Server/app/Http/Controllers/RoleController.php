<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoleResource;
use App\Services\RoleService;
use Illuminate\Http\Request;
use App\Http\Requests\RoleUpdateRequest;
use Spatie\Permission\Models\Role;

class RoleController
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index()
    {
        $roles = $this->roleService->getAll();
        return RoleResource::collection($roles);
    }

    public function show(Role $role)
    {
        return new RoleResource($role);
    }

    public function update(RoleUpdateRequest $request, Role $role)
    {
        $updatedRole = $this->roleService->update($role, $request->validated());
        return new RoleResource($updatedRole);
    }

    public function destroy(Role $role)
    {
        return $this->roleService->destroy($role);
    }
}
