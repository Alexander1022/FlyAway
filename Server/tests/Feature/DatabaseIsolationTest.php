<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class DatabaseIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_uses_separate_test_database()
    {
        // Verify we're using the correct database connection
        $this->assertEquals('sqlite', Config::get('database.default'));
        
        // In testing environment, the database should be :memory:
        $databasePath = Config::get('database.connections.sqlite.database');
        $this->assertEquals(':memory:', $databasePath);
    }

    public function test_database_is_fresh_for_each_test()
    {
        // Create a test record
        DB::table('users')->insert([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Verify it exists
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com'
        ]);
    }

    public function test_database_is_fresh_for_second_test()
    {
        // This test should not see the user from the previous test
        // because RefreshDatabase cleans the database between tests
        $this->assertDatabaseMissing('users', [
            'email' => 'test@example.com'
        ]);
    }
}
