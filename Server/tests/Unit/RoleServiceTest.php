<?php

namespace Tests\Unit;

use Spatie\Permission\Models\Role;
use App\Services\RoleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleServiceTest extends TestCase
{
    use RefreshDatabase;

    private RoleService $roleService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->roleService = new RoleService();
    }

    public function test_can_update_role()
    {
        $role = Role::create(['name' => 'original_name']);

        $updateData = ['name' => 'updated_name'];
        $result = $this->roleService->update($role, $updateData);

        $this->assertInstanceOf(Role::class, $result);
        $this->assertEquals('updated_name', $result->name);
        $role->refresh();
        $this->assertEquals('updated_name', $role->name);
    }

    public function test_can_destroy_role()
    {
        $role = Role::create(['name' => 'to_delete']);
        $roleId = $role->id;

        $response = $this->roleService->destroy($role);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Role deleted successfully.', $responseData['message']);
        $this->assertDatabaseMissing('roles', ['id' => $roleId]);
    }
}
