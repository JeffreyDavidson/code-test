<?php

namespace Tests\Feature;

use App\User;
use App\Product;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function testsProductsAreCreatedCorrectly()
    {
        $user = factory(User::class)->create();
        $token = $user->generateToken();
        $headers = ['Authorization' => "Bearer $token"];
        $payload = [
            'name' => 'Lorem',
            'description' => 'Ipsum',
            'price' => 8900,
            'image' => 'lorem.jpg',
        ];

        $this->json('POST', '/api/products', $payload, $headers)
            ->assertStatus(201)
            ->assertJson(['id' => 1, 'name' => 'Lorem', 'description' => 'Ipsum', 'price' => 8900, 'image' => 'lorem.jpg', 'user_id' => $user->id]);
    }
}
