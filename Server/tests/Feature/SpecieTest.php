<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Specie;
use App\Models\SpecieType;
use App\Models\Habitat;
use App\Models\SpecieKingdom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SpecieTest extends TestCase
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

    public function test_authenticated_user_can_get_species()
    {
        [$user, $token] = $this->createAuthenticatedUser();
        Specie::factory()->count(3)->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->getJson('/api/species');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'common_name',
                            'scientific_name',
                        ]
                    ]
                ]);
    }

    public function test_authenticated_user_can_get_species_dropdown()
    {
        [$user, $token] = $this->createAuthenticatedUser();
        Specie::factory()->count(3)->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->getJson('/api/species/dropdown');

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

    public function test_authenticated_user_can_get_specific_specie()
    {
        [$user, $token] = $this->createAuthenticatedUser();
        $specie = Specie::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->getJson("/api/species/{$specie->id}");
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'common_name',
                        'scientific_name',
                    ]
                ]);
    }

    public function test_admin_can_create_specie()
    {
        [$admin, $token] = $this->createAdminUser();
        
        // Create required related models
        $specieKingdom = SpecieKingdom::factory()->create();
        $habitat = Habitat::factory()->create();
        $specieType = SpecieType::factory()->create();

        // Create a fake image file
        $image = UploadedFile::fake()->image('test.jpg');

        $specieData = [
            'specie_kingdom_id' => $specieKingdom->id,
            'habitat_id' => $habitat->id,
            'common_name' => $this->faker->word,
            'scientific_name' => $this->faker->word . ' ' . $this->faker->word,
            'specie_type_ids' => [$specieType->id],
            'image' => $image,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->postJson('/api/species', $specieData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'common_name',
                        'scientific_name',
                    ]
                ]);

        $this->assertDatabaseHas('species', [
            'common_name' => $specieData['common_name'],
            'scientific_name' => $specieData['scientific_name'],
        ]);
    }

    public function test_regular_user_cannot_create_specie()
    {
        [$user, $token] = $this->createAuthenticatedUser();
        
        // Create required related models
        $specieKingdom = SpecieKingdom::factory()->create();
        $habitat = Habitat::factory()->create();
        $specieType = SpecieType::factory()->create();

        // Create a fake image file
        $image = UploadedFile::fake()->image('test.jpg');

        $specieData = [
            'specie_kingdom_id' => $specieKingdom->id,
            'habitat_id' => $habitat->id,
            'common_name' => $this->faker->word,
            'scientific_name' => $this->faker->word . ' ' . $this->faker->word,
            'specie_type_ids' => [$specieType->id],
            'image' => $image,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->postJson('/api/species', $specieData);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_specie()
    {
        [$admin, $token] = $this->createAdminUser();
        $specie = Specie::factory()->create();
        
        // Create required related models
        $specieKingdom = SpecieKingdom::factory()->create();
        $habitat = Habitat::factory()->create();
        $specieType = SpecieType::factory()->create();

        // Create a fake image file
        $image = UploadedFile::fake()->image('test.jpg');

        $updateData = [
            'specie_kingdom_id' => $specieKingdom->id,
            'habitat_id' => $habitat->id,
            'common_name' => 'Updated Specie Name',
            'scientific_name' => 'Updated Scientific Name',
            'specie_type_ids' => [$specieType->id],
            'image' => $image,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->putJson("/api/species/{$specie->id}", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('species', [
            'id' => $specie->id,
            'common_name' => 'Updated Specie Name',
            'scientific_name' => 'Updated Scientific Name',
        ]);
    }

    public function test_regular_user_cannot_update_specie()
    {
        [$user, $token] = $this->createAuthenticatedUser();
        $specie = Specie::factory()->create();
        
        // Create required related models
        $specieKingdom = SpecieKingdom::factory()->create();
        $habitat = Habitat::factory()->create();
        $specieType = SpecieType::factory()->create();

        // Create a fake image file
        $image = UploadedFile::fake()->image('test.jpg');

        $updateData = [
            'specie_kingdom_id' => $specieKingdom->id,
            'habitat_id' => $habitat->id,
            'common_name' => 'Updated Specie Name',
            'scientific_name' => 'Updated Scientific Name',
            'specie_type_ids' => [$specieType->id],
            'image' => $image,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->putJson("/api/species/{$specie->id}", $updateData);

        $response->assertStatus(403);
    }

    public function test_admin_can_delete_specie()
    {
        [$admin, $token] = $this->createAdminUser();

        $specie = Specie::factory()->create();
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->deleteJson("/api/species/{$specie->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('species', [
            'id' => $specie->id,
        ]);
    }

    public function test_regular_user_cannot_delete_specie()
    {
        [$user, $token] = $this->createAuthenticatedUser();
        $specie = Specie::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->deleteJson("/api/species/{$specie->id}");

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_access_species()
    {
        $response = $this->getJson('/api/species');

        $response->assertStatus(401);
    }

    public function test_specie_creation_requires_valid_data()
    {
        [$admin, $token] = $this->createAdminUser();

        $invalidData = [
            'common_name' => '', // Empty common name
            'scientific_name' => '', // Empty scientific name
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->postJson('/api/species', $invalidData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['common_name', 'scientific_name']);
    }
}
