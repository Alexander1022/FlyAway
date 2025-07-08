<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Habitat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HabitatTest extends TestCase
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

    public function test_authenticated_user_can_get_habitats()
    {
        [$user, $token] = $this->createAuthenticatedUser();
        Habitat::factory()->count(3)->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->getJson('/api/habitats');

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

    public function test_authenticated_user_can_get_habitats_dropdown()
    {
        [$user, $token] = $this->createAuthenticatedUser();
        Habitat::factory()->count(3)->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->getJson('/api/habitats/dropdown');

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

    public function test_authenticated_user_can_get_specific_habitat()
    {
        [$user, $token] = $this->createAuthenticatedUser();
        $habitat = Habitat::factory()->create();
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->getJson("/api/habitats/{$habitat->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                    ]
                ]);
    }

    public function test_admin_can_create_habitat()
    {
        [$admin, $token] = $this->createAdminUser();

        $habitatData = [
            'name' => $this->faker->word,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->postJson('/api/habitats', $habitatData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                    ]
                ]);

        $this->assertDatabaseHas('habitats', [
            'name' => $habitatData['name'],
        ]);
    }

    public function test_regular_user_cannot_create_habitat()
    {
        [$user, $token] = $this->createAuthenticatedUser();

        $habitatData = [
            'name' => $this->faker->word,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->postJson('/api/habitats', $habitatData);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_habitat()
    {
        [$admin, $token] = $this->createAdminUser();
        $habitat = Habitat::factory()->create();

        $updateData = [
            'name' => 'Updated Habitat Name',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->putJson("/api/habitats/{$habitat->id}", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('habitats', [
            'id' => $habitat->id,
            'name' => 'Updated Habitat Name',
        ]);
    }

    public function test_regular_user_cannot_update_habitat()
    {
        [$user, $token] = $this->createAuthenticatedUser();
        $habitat = Habitat::factory()->create();

        $updateData = [
            'name' => 'Updated Habitat Name',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->putJson("/api/habitats/{$habitat->id}", $updateData);

        $response->assertStatus(403);
    }

    public function test_admin_can_delete_habitat()
    {
        [$admin, $token] = $this->createAdminUser();
        $habitat = Habitat::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->deleteJson("/api/habitats/{$habitat->id}");
        $response->assertStatus(200);

        $this->assertDatabaseMissing('habitats', [
            'id' => $habitat->id,
        ]);
    }

    public function test_regular_user_cannot_delete_habitat()
    {
        [$user, $token] = $this->createAuthenticatedUser();
        $habitat = Habitat::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->deleteJson("/api/habitats/{$habitat->id}");

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_access_habitats()
    {
        $response = $this->getJson('/api/habitats');

        $response->assertStatus(401);
    }

    public function test_habitat_creation_requires_valid_data()
    {
        [$admin, $token] = $this->createAdminUser();

        $invalidData = [
            'name' => '', // Empty name
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->postJson('/api/habitats', $invalidData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
    }
}
