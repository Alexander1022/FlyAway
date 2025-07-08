<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    private UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userService = new UserService();
    }

    public function test_can_create_user()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'xp' => 100,
        ];

        $user = $this->userService->createUser($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($userData['name'], $user->name);
        $this->assertEquals($userData['email'], $user->email);
        $this->assertEquals($userData['xp'], $user->xp);
        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
            'xp' => $userData['xp'],
        ]);
    }

    public function test_can_update_user()
    {
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
            'xp' => 50,
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'xp' => 150,
        ];

        $result = $this->userService->updateUser($user, $updateData);

        $this->assertTrue($result);
        $user->refresh();
        $this->assertEquals($updateData['name'], $user->name);
        $this->assertEquals($updateData['xp'], $user->xp);
        $this->assertEquals('original@example.com', $user->email); // Email should remain unchanged
    }

    public function test_can_delete_user()
    {
        $user = User::factory()->create();
        $userId = $user->id;

        $result = $this->userService->deleteUser($user);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('users', ['id' => $userId]);
    }

    public function test_update_user_throws_exception_on_unique_constraint_violation()
    {
        $user = User::factory()->create();
        
        // Try to update with invalid data (duplicate email)
        $existingUser = User::factory()->create(['email' => 'existing@example.com']);
        
        $updateData = [
            'email' => 'existing@example.com', // This should cause a failure due to unique constraint
        ];

        $this->expectException(\Illuminate\Database\UniqueConstraintViolationException::class);
        $this->userService->updateUser($user, $updateData);
    }
}
