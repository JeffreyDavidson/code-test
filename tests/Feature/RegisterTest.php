<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function testsRegistersSuccessfully()
    {
        $payload = [
            'first_name' => 'Jeffrey',
            'last_name' => 'Davidson',
            'email' => 'jeffrey@example.com',
            'password' => 'testpass123',
            'password_confirmation' => 'testpass123',
        ];

        $this->json('post', '/api/register', $payload)
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'first_name',
                    'last_name',
                    'email',
                    'created_at',
                    'updated_at',
                    'api_token',
                ],
            ]);
        ;
    }

    public function testsRequiresPasswordEmailAndName()
    {
        $this->json('post', '/api/register')
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                     'first_name' => ['The first name field is required.'],
                    'last_name' => ['The last name field is required.'],
                    'email' => ['The email field is required.'],
                    'password' => ['The password field is required.'],
                ]
            ]);
    }

    public function testsRequirePasswordConfirmation()
    {
        $payload = [
            'first_name' => 'Jeffrey',
            'last_name' => 'Davidson',
            'email' => 'jeffrey@example.com',
            'password' => 'testpass123',
        ];

        $this->json('post', '/api/register', $payload)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'password' => ['The password confirmation does not match.'],
                ]
            ]);
    }
}
