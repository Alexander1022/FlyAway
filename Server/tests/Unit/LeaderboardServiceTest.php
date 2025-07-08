<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\LeaderboardService;
use App\Http\Resources\LeaderboardResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaderboardServiceTest extends TestCase
{
    use RefreshDatabase;

    private LeaderboardService $leaderboardService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->leaderboardService = new LeaderboardService();
    }

    public function test_can_get_leaderboard()
    {
        $users = User::factory()->count(3)->create();

        $result = $this->leaderboardService->getLeaderboard();

        $this->assertInstanceOf(\Illuminate\Http\Resources\Json\AnonymousResourceCollection::class, $result);
        $this->assertCount(3, $result->collection);
    }

    public function test_can_update_user_in_leaderboard()
    {
        $user = User::factory()->create(['name' => 'Original Name', 'xp' => 100]);

        $updateData = ['name' => 'Updated Name', 'xp' => 200];
        $result = $this->leaderboardService->update($user->id, $updateData);

        $this->assertInstanceOf(LeaderboardResource::class, $result);
        $user->refresh();
        $this->assertEquals('Updated Name', $user->name);
        $this->assertEquals(200, $user->xp);
    }

    public function test_update_returns_404_for_non_existent_user()
    {
        $response = $this->leaderboardService->update(999, ['name' => 'Test']);

        $this->assertEquals(404, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('User not found', $responseData['message']);
    }

    public function test_can_destroy_user_from_leaderboard()
    {
        $user = User::factory()->create();
        $userId = $user->id;

        $response = $this->leaderboardService->destroy($user->id);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('User removed from leaderboard successfully.', $responseData['message']);
        $this->assertDatabaseMissing('users', ['id' => $userId]);
    }

    public function test_destroy_returns_404_for_non_existent_user()
    {
        $response = $this->leaderboardService->destroy(999);

        $this->assertEquals(404, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('User not found', $responseData['message']);
    }
}
