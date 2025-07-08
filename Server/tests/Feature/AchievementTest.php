<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Achievement;
use App\Models\UserAchievement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\SpecieType;

class AchievementTest extends TestCase
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

    public function test_authenticated_user_can_get_achievements()
    {
        [, $token] = $this->createAuthenticatedUser();
        Achievement::factory()->count(3)->create();

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/achievements');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'points_to_complete',
                        'reward_xp',
                        'created_at',
                        'updated_at',
                        'users',
                        'specie_types',
                    ]
                ]
            ]);
    }

    public function test_authenticated_user_can_get_specific_achievement()
    {
        [$user, $token] = $this->createAuthenticatedUser();
        $achievement = Achievement::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/achievements/{$achievement->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'points_to_complete',
                    'reward_xp',
                    'created_at',
                    'updated_at',
                    'users',
                    'specie_types',
                ]
            ]);
    }

    public function test_admin_can_create_achievement()
    {
        [$admin, $token] = $this->createAdminUser();

        $specieType = SpecieType::factory()->create();

        $achievementData = [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->text(200),
            'points_to_complete' => $this->faker->numberBetween(10, 100),
            'reward_xp' => $this->faker->numberBetween(50, 200),
            'specie_types' => [$specieType->id],
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/achievements', $achievementData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'points_to_complete',
                ]
            ]);

        $this->assertDatabaseHas('achievements', [
            'name' => $achievementData['name'],
            'description' => $achievementData['description'],
            'points_to_complete' => $achievementData['points_to_complete'],
            'reward_xp' => $achievementData['reward_xp'],
        ])->assertDatabaseHas('achievement_specie_type', [
                    'achievement_id' => $response->json('data.id'),
                    'specie_type_id' => $specieType->id,
                ]);
    }

    public function test_regular_user_cannot_create_achievement()
    {
        [$user, $token] = $this->createAuthenticatedUser();

        $specieType = SpecieType::factory()->create();

        $achievementData = [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->text(200),
            'points_to_complete' => $this->faker->numberBetween(10, 100),
            'reward_xp' => $this->faker->numberBetween(50, 200),
            'specie_types' => [$specieType->id],
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/achievements', $achievementData);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_achievement()
    {
        [$admin, $token] = $this->createAdminUser();
        $achievement = Achievement::factory()->create();
        $specieType = SpecieType::factory()->create();

        $updateData = [
            'name' => 'Updated Achievement Name',
            'description' => 'Updated description',
            'points_to_complete' => 50,
            'reward_xp' => 100,
            'specie_types' => [$specieType->id], // Assuming no change in specie types for this test
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/achievements/{$achievement->id}", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('achievements', [
            'id' => $achievement->id,
            'name' => 'Updated Achievement Name',
            'description' => 'Updated description',
            'points_to_complete' => 50,
            'reward_xp' => 100,
        ])->assertDatabaseHas('achievement_specie_type', [
                    'achievement_id' => $response->json('data.id'),
                    'specie_type_id' => $specieType->id,
                ])
        ;
    }

    public function test_regular_user_cannot_update_achievement()
    {
        [$user, $token] = $this->createAuthenticatedUser();
        $achievement = Achievement::factory()->create();

        $updateData = [
            'name' => 'Updated Achievement Name',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/achievements/{$achievement->id}", $updateData);

        $response->assertStatus(403);
    }

    public function test_admin_can_delete_achievement()
    {
        [$admin, $token] = $this->createAdminUser();
        $achievement = Achievement::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/achievements/{$achievement->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('achievements', [
            'id' => $achievement->id,
        ]);
    }

    public function test_regular_user_cannot_delete_achievement()
    {
        [$user, $token] = $this->createAuthenticatedUser();
        $achievement = Achievement::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/achievements/{$achievement->id}");

        $response->assertStatus(403);
    }

    public function test_authenticated_user_can_be_assigned_achievement()
    {
        [$user, $token] = $this->createAuthenticatedUser();
        $achievement = Achievement::factory()->create();

        $assignmentData = [
            'achievement_id' => $achievement->id,
            'points' => 25,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/assign-achievement-points', $assignmentData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('user_achievement', [
            'user_id' => $user->id,
            'achievement_id' => $achievement->id,
            'points' => 25,
        ]);
    }

    public function test_unauthenticated_user_cannot_access_achievements()
    {
        $response = $this->getJson('/api/achievements');

        $response->assertStatus(401);
    }

    public function test_achievement_creation_requires_valid_data()
    {
        [$admin, $token] = $this->createAdminUser();

        $invalidData = [
            'name' => '',
            'points_to_complete' => 'invalid',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/achievements', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'points_to_complete']);
    }

    public function test_achievement_points_assignment_requires_valid_data()
    {
        [$user, $token] = $this->createAuthenticatedUser();

        $invalidData = [
            'achievement_id' => 'invalid',
            'points' => 'invalid',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/assign-achievement-points', $invalidData);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['achievement_id', 'points']);
    }
}
