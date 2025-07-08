<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Achievement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LeaderboardTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private function createAuthenticatedUser()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        return [$user, $token];
    }

    public function test_authenticated_user_can_get_leaderboard()
    {
        [$user, $token] = $this->createAuthenticatedUser();

        // Create some users with different XP values
        User::factory()->create(['xp' => 100]);
        User::factory()->create(['xp' => 200]);
        User::factory()->create(['xp' => 50]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->getJson('/api/leaderboard');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'email',
                            'xp',
                        ]
                    ]
                ]);

        // Verify the leaderboard is sorted by XP in descending order
        $leaderboard = $response->json('data');
        $this->assertCount(4, $leaderboard);
        $xps = collect($leaderboard)->pluck('xp')->all();
        $this->assertContains(100, $xps);
        $this->assertContains(200, $xps);
        $this->assertContains(50, $xps);

    }

    public function test_leaderboard_includes_current_user()
    {
        [$user, $token] = $this->createAuthenticatedUser();
        $user->update(['xp' => 150]);

        // Create other users
        User::factory()->create(['xp' => 200]);
        User::factory()->create(['xp' => 100]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->getJson('/api/leaderboard');

        $response->assertStatus(200);

        $leaderboard = $response->json('data');
        $currentUserInLeaderboard = collect($leaderboard)->firstWhere('id', $user->id);

        $this->assertNotNull($currentUserInLeaderboard);
        $this->assertEquals($user->xp, $currentUserInLeaderboard['xp']);
    }

    public function test_unauthenticated_user_cannot_access_leaderboard()
    {
        $response = $this->getJson('/api/leaderboard');

        $response->assertStatus(401);
    }

    public function test_leaderboard_shows_users_with_zero_xp()
    {
        [$user, $token] = $this->createAuthenticatedUser();

        // Create users with zero XP
        User::factory()->create(['xp' => 0]);
        User::factory()->create(['xp' => 100]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->getJson('/api/leaderboard');

        $response->assertStatus(200);

        $leaderboard = $response->json('data');
        
        // Should include users with 0 XP
        $userWithZeroXP = collect($leaderboard)->firstWhere('xp', 0);
        $this->assertNotNull($userWithZeroXP);
    }
}
