<?php

namespace Tests\Unit;

use App\Http\Requests\UserRegistrationRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UserRegistrationRequestTest extends TestCase
{
    public function test_valid_registration_data_passes_validation()
    {
        $request = new UserRegistrationRequest();
        
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $validator = Validator::make($data, $request->rules());
        
        $this->assertTrue($validator->passes());
    }

    public function test_name_is_required()
    {
        $request = new UserRegistrationRequest();
        
        $data = [
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $validator = Validator::make($data, $request->rules());
        
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function test_email_is_required()
    {
        $request = new UserRegistrationRequest();
        
        $data = [
            'name' => 'John Doe',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $validator = Validator::make($data, $request->rules());
        
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    public function test_password_is_required()
    {
        $request = new UserRegistrationRequest();
        
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ];

        $validator = Validator::make($data, $request->rules());
        
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    public function test_email_must_be_valid_format()
    {
        $request = new UserRegistrationRequest();
        
        $data = [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $validator = Validator::make($data, $request->rules());
        
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    public function test_email_must_be_unique()
    {
        $request = new UserRegistrationRequest();
        
        // Create a user with the email first
        \App\Models\User::factory()->create(['email' => 'existing@example.com']);
        
        $data = [
            'name' => 'John Doe',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $validator = Validator::make($data, $request->rules());
        
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    public function test_password_must_be_at_least_8_characters()
    {
        $request = new UserRegistrationRequest();
        
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ];

        $validator = Validator::make($data, $request->rules());
        
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    public function test_password_must_be_confirmed()
    {
        $request = new UserRegistrationRequest();
        
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different123',
        ];

        $validator = Validator::make($data, $request->rules());
        
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    public function test_name_cannot_exceed_255_characters()
    {
        $request = new UserRegistrationRequest();
        
        $data = [
            'name' => str_repeat('a', 256), // 256 characters
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $validator = Validator::make($data, $request->rules());
        
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }
}
