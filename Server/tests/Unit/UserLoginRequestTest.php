<?php

namespace Tests\Unit;

use App\Http\Requests\UserLoginRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UserLoginRequestTest extends TestCase
{
    public function test_valid_login_data_passes_validation()
    {
        $request = new UserLoginRequest();
        
        $data = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        // Create a user first to satisfy the exists rule
        \App\Models\User::factory()->create(['email' => 'test@example.com']);

        $validator = Validator::make($data, $request->rules());
        
        $this->assertTrue($validator->passes());
    }

    public function test_email_is_required()
    {
        $request = new UserLoginRequest();
        
        $data = [
            'password' => 'password123',
        ];

        $validator = Validator::make($data, $request->rules());
        
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    public function test_password_is_required()
    {
        $request = new UserLoginRequest();
        
        $data = [
            'email' => 'test@example.com',
        ];

        $validator = Validator::make($data, $request->rules());
        
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    public function test_email_must_be_valid_format()
    {
        $request = new UserLoginRequest();
        
        $data = [
            'email' => 'invalid-email',
            'password' => 'password123',
        ];

        $validator = Validator::make($data, $request->rules());
        
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    public function test_email_must_exist_in_users_table()
    {
        $request = new UserLoginRequest();
        
        $data = [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ];

        $validator = Validator::make($data, $request->rules());
        
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    public function test_password_must_be_at_least_8_characters()
    {
        $request = new UserLoginRequest();
        
        $data = [
            'email' => 'test@example.com',
            'password' => 'short',
        ];

        \App\Models\User::factory()->create(['email' => 'test@example.com']);

        $validator = Validator::make($data, $request->rules());
        
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }
}
