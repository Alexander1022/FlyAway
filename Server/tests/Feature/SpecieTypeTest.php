<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SpecieType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SpecieTypeTest extends TestCase
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

    public function test_authenticated_user_can_get_specie_types()
    {
        [$user, $token] = $this->createAuthenticatedUser();
        SpecieType::factory()->count(3)->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->getJson('/api/specie-types');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                        ]
                    ]
                ]);
    }

    public function test_authenticated_user_can_get_specie_types_dropdown()
    {
        [$user, $token] = $this->createAuthenticatedUser();
        SpecieType::factory()->count(3)->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->getJson('/api/specie-types/dropdown');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                        ]
                    ]
                ]);
    }

    public function test_authenticated_user_can_get_specific_specie_type()
    {
        [$user, $token] = $this->createAuthenticatedUser();
        $specieType = SpecieType::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->getJson("/api/specie-types/{$specieType->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                    ]
                ]);
    }

    public function test_admin_can_create_specie_type()
    {
        [$admin, $token] = $this->createAdminUser();

        $specieTypeData = [
            'name' => $this->faker->word,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->postJson('/api/specie-types', $specieTypeData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                    ]
                ]);

        $this->assertDatabaseHas('specie_types', [
            'name' => $specieTypeData['name'],
        ]);
    }

    public function test_regular_user_cannot_create_specie_type()
    {
        [$user, $token] = $this->createAuthenticatedUser();

        $specieTypeData = [
            'name' => $this->faker->word,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->postJson('/api/specie-types', $specieTypeData);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_specie_type()
    {
        [$admin, $token] = $this->createAdminUser();
        $specieType = SpecieType::factory()->create();

        $updateData = [
            'name' => 'Updated Specie Type Name',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->putJson("/api/specie-types/{$specieType->id}", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('specie_types', [
            'id' => $specieType->id,
            'name' => 'Updated Specie Type Name',
        ]);
    }

    public function test_regular_user_cannot_update_specie_type()
    {
        [$user, $token] = $this->createAuthenticatedUser();
        $specieType = SpecieType::factory()->create();

        $updateData = [
            'name' => 'Updated Specie Type Name',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->putJson("/api/specie-types/{$specieType->id}", $updateData);

        $response->assertStatus(403);
    }

    public function test_admin_can_delete_specie_type()
    {
        [$admin, $token] = $this->createAdminUser();
        $specieType = SpecieType::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->deleteJson("/api/specie-types/{$specieType->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('specie_types', [
            'id' => $specieType->id,
        ]);
    }

    public function test_regular_user_cannot_delete_specie_type()
    {
        [$user, $token] = $this->createAuthenticatedUser();
        $specieType = SpecieType::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->deleteJson("/api/specie-types/{$specieType->id}");

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_access_specie_types()
    {
        $response = $this->getJson('/api/specie-types');

        $response->assertStatus(401);
    }

    public function test_specie_type_creation_requires_valid_data()
    {
        [$admin, $token] = $this->createAdminUser();

        $invalidData = [
            'name' => '',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->postJson('/api/specie-types', $invalidData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
    }
}
