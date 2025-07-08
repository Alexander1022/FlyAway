<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\SpecieType;

class ApiEndpointsBasicTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function createAuthenticatedUser()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        return [$user, $token];
    }

    protected function createAdminUser()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $token = $user->createToken('test-token')->plainTextToken;
        return [$user, $token];
    }

    public function test_user_can_register()
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ]
                ]);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'token',
                    'user',
                ]);
    }

    public function test_authenticated_user_can_logout()
    {
        [$user, $token] = $this->createAuthenticatedUser();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->postJson('/api/logout');

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_get_their_profile()
    {
        [$user, $token] = $this->createAuthenticatedUser();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->getJson('/api/user');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                        'email',
                    ]
                ]);
    }

    public function test_authenticated_user_can_get_locations()
    {
        [$user, $token] = $this->createAuthenticatedUser();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->getJson('/api/locations');

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_get_their_locations()
    {
        [$user, $token] = $this->createAuthenticatedUser();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->getJson('/api/user/my-locations');

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_get_species()
    {
        [$user, $token] = $this->createAuthenticatedUser();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->getJson('/api/species');

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_get_species_dropdown()
    {
        [$user, $token] = $this->createAuthenticatedUser();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->getJson('/api/species/dropdown');

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_get_specie_types()
    {
        [$user, $token] = $this->createAuthenticatedUser();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->getJson('/api/specie-types');

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_get_specie_types_dropdown()
    {
        [$user, $token] = $this->createAuthenticatedUser();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->getJson('/api/specie-types/dropdown');

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_get_habitats()
    {
        [$user, $token] = $this->createAuthenticatedUser();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->getJson('/api/habitats');

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_get_habitats_dropdown()
    {
        [$user, $token] = $this->createAuthenticatedUser();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->getJson('/api/habitats/dropdown');

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_get_achievements()
    {
        [$user, $token] = $this->createAuthenticatedUser();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->getJson('/api/achievements');

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_get_leaderboard()
    {
        [$user, $token] = $this->createAuthenticatedUser();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->getJson('/api/leaderboard');

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_get_roles()
    {
        [$user, $token] = $this->createAuthenticatedUser();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->getJson('/api/roles');

        $response->assertStatus(200);
    }

    public function test_location_creation_requires_authentication()
    {
        Storage::fake('public');

        $response = $this->postJson('/api/locations', [
            'lat' => 45.5231,
            'lng' => -122.6765,
            'images' => [UploadedFile::fake()->image('test.jpg')],
            'specie_kingdom' => 'animal',
        ]);

        $response->assertStatus(401);
    }

    public function test_location_creation_requires_valid_data()
    {
        [$user, $token] = $this->createAuthenticatedUser();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->postJson('/api/locations', [
                            'lat' => '', // Invalid
                            'lng' => '', // Invalid
                            // Missing required fields
                        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['lat', 'lng', 'images', 'specie_kingdom']);
    }

    public function test_unauthenticated_requests_are_rejected()
    {
        $protectedEndpoints = [
            ['GET', '/api/user'],
            ['POST', '/api/logout'],
            ['GET', '/api/locations'],
            ['GET', '/api/species'],
            ['GET', '/api/achievements'],
            ['GET', '/api/leaderboard'],
            ['GET', '/api/roles'],
        ];

        foreach ($protectedEndpoints as [$method, $endpoint]) {
            $response = $this->json($method, $endpoint);
            $response->assertStatus(401);
        }
    }

    public function test_admin_endpoints_require_admin_role()
    {
        [$user, $token] = $this->createAuthenticatedUser();

        $adminEndpoints = [
            ['POST', '/api/species', ['common_name' => 'Test', 'scientific_name' => 'Test scientificus']],
            ['POST', '/api/specie-types', ['name' => 'Test Type']],
            ['POST', '/api/habitats', ['name' => 'Test Habitat']],
            ['POST', '/api/achievements', ['name' => 'Test Achievement', 'description' => 'Test', 'points_to_complete' => 10, 'reward_xp' => 5]],
        ];

        foreach ($adminEndpoints as [$method, $url, $data]) {
            $response = $this->json($method, $url, $data, ['Authorization' => 'Bearer ' . $token]);
            $response->assertStatus(403);
        }
    }

    public function test_admin_can_access_admin_endpoints()
    {
        [$admin, $token] = $this->createAdminUser();

        $specieType = SpecieType::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->postJson('/api/achievements', [
                            'name' => 'Test Achievement',
                            'description' => 'Test Description',
                            'points_to_complete' => 10,
                            'reward_xp' => 5,
                            'specie_types' => [$specieType->id],
                        ]);

        $response->assertStatus(201);
    }
}
