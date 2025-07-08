<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private function createAuthenticatedUser()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        return [$user, $token];
    }

    private function createAdminUser()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $token = $user->createToken('test-token')->plainTextToken;
        return [$user, $token];
    }

    public function test_authenticated_user_can_get_roles()
    {
        [$user, $token] = $this->createAuthenticatedUser();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->getJson('/api/roles');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'guard_name',
                        ]
                    ]
                ]);
    }

    public function test_authenticated_user_can_get_specific_role()
    {
        [$user, $token] = $this->createAuthenticatedUser();
        $role = \Spatie\Permission\Models\Role::create(['name' => 'test-role']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->getJson("/api/roles/{$role->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                        'guard_name',
                    ]
                ]);
    }

    public function test_admin_can_update_role()
    {
        [$admin, $token] = $this->createAdminUser();
        $role = \Spatie\Permission\Models\Role::create(['name' => 'test-role']);

        $updateData = [
            'name' => 'updated-role-name',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->putJson("/api/roles/{$role->id}", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'updated-role-name',
        ]);
    }

    public function test_regular_user_cannot_update_role()
    {
        [$user, $token] = $this->createAuthenticatedUser();
        $role = \Spatie\Permission\Models\Role::create(['name' => 'test-role']);

        $updateData = [
            'name' => 'updated-role-name',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->putJson("/api/roles/{$role->id}", $updateData);

        $response->assertStatus(403);
    }

    public function test_admin_can_delete_role()
    {
        [$admin, $token] = $this->createAdminUser();
        $role = \Spatie\Permission\Models\Role::create(['name' => 'test-role']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->deleteJson("/api/roles/{$role->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('roles', [
            'id' => $role->id,
        ]);
    }

    public function test_regular_user_cannot_delete_role()
    {
        [$user, $token] = $this->createAuthenticatedUser();
        $role = \Spatie\Permission\Models\Role::create(['name' => 'test-role']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->deleteJson("/api/roles/{$role->id}");

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_access_roles()
    {
        $response = $this->getJson('/api/roles');

        $response->assertStatus(401);
    }

    public function test_role_update_requires_valid_data()
    {
        [$admin, $token] = $this->createAdminUser();
        $role = \Spatie\Permission\Models\Role::create(['name' => 'test-role']);

        $invalidData = [
            'name' => '', // Empty name
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->putJson("/api/roles/{$role->id}", $invalidData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
    }
}
