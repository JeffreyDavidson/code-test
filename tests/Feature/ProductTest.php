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

    public function testsArticlesAreUpdatedCorrectly()
    {
        $user = factory(User::class)->create();
        $token = $user->generateToken();
        $headers = ['Authorization' => "Bearer $token"];
        $product = factory(Product::class)->create([
            'name' => 'Old Name',
            'description' => 'Old Desciption',
            'price' => 8900,
            'image' => 'old-product-image.jpg'
        ]);

        $payload = [
            'name' => 'New Name',
            'description' => 'New Description',
            'price' => '7200',
            'image' => 'new-product-image.jpg',
        ];

        $response = $this->json('PUT', '/api/products/' . $product->id, $payload, $headers)
            ->assertStatus(200)
            ->assertJson([
                'id' => 1,
                'name' => 'New Name',
                'description' => 'New Description',
                'price' => 7200,
                'image' => 'new-product-image.jpg',
    public function testProductsAreListedByAUserCorrectly()
    {
        $user = factory(User::class)->create();
        $token = $user->generateToken();

        factory(Product::class)->create([
            'user_id' => $user,
            'name' => 'First Product',
            'description' => 'First Description',
        ]);

        factory(Product::class)->create([
            'name' => 'Second Product',
            'description' => 'Second Description'
        ]);

        $headers = ['Authorization' => "Bearer $token"];

        $response = $this->json('GET', '/api/products', [], $headers)
            ->assertStatus(200)
            ->assertJson([
                ['name' => 'First Product', 'description' => 'First Description'],
            ])
            ->assertJsonStructure([
                '*' => ['id', 'description', 'name'],
            ]);
    }

    public function testsProductsAreDeletedCorrectly()
    {
        $user = factory(User::class)->create();
        $token = $user->generateToken();
        $headers = ['Authorization' => "Bearer $token"];
        $product = factory(Product::class)->create([
            'name' => 'First Product',
            'description' => 'First Description',
        ]);

        $this->json('DELETE', '/api/products/' . $product->id, [], $headers)->assertStatus(204);
        $this->assertDeleted('products', $product->toArray());
    }
}
