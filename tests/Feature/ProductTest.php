<?php

namespace Tests\Feature;

use App\User;
use App\Product;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function testsProductsForUsersWithAnActiveSubscriptionAreCreatedCorrectly()
    {
        Storage::fake('products');

        $file = UploadedFile::fake()->image('lorem.jpg');
        $user = factory(User::class)->states('withActiveSubscription')->create();
        $token = $user->generateToken();

        $headers = ['Authorization' => "Bearer $token"];
        $payload = [
            'name' => 'Lorem',
            'description' => 'Ipsum',
            'price' => 8900,
            'image' => $file,
        ];

        $this->json('POST', '/api/products', $payload, $headers)
            ->assertStatus(201)
            ->assertJson([
                'id' => 1,
                'name' => 'Lorem',
                'description' => 'Ipsum',
                'price' => 8900,
                'image' => 'products/'.$file->getClientOriginalName(),
            ]);
    }

    public function testsProductsForUsersWithoutAnActiveSubscriptionAreCreatedCorrectly()
    {
        Storage::fake('products');

        $file = UploadedFile::fake()->image('lorem.jpg');
        $user = factory(User::class)->create();
        $token = $user->generateToken();

        $headers = ['Authorization' => "Bearer $token"];
        $payload = [
            'name' => 'Lorem',
            'description' => 'Ipsum',
            'price' => 8900,
            'image' => $file,
        ];

        $this->json('POST', '/api/products', $payload, $headers)->assertForbidden();
    }

    public function testsProductsAreUpdatedCorrectly()
    {
        Storage::fake('products');

        $file = UploadedFile::fake()->image('new-product-image.jpg');
        $user = factory(User::class)->create();
        $token = $user->generateToken();
        $headers = ['Authorization' => "Bearer $token"];
        $product = factory(Product::class)->create([
            'name' => 'Old Name',
            'description' => 'Old Desciption',
            'price' => 8900,
            'image' => 'old-product-image.jpg'
        ]);
        $user->products()->attach($product);

        $payload = [
            'name' => 'New Name',
            'description' => 'New Description',
            'price' => 7200,
            'image' => $file,
        ];

        $response = $this->json('PUT', '/api/products/' . $product->id, $payload, $headers)
            ->assertStatus(200)
            ->assertJson([
                'id' => 1,
                'name' => 'New Name',
                'description' => 'New Description',
                'price' => 7200,
                'image' => 'products/'.$file->getClientOriginalName(),
            ]);
    }

    public function testProductsAreListedByAUserCorrectly()
    {
        $user = factory(User::class)->create();
        $token = $user->generateToken();

        $product = factory(Product::class)->create([
            'name' => 'First Product',
            'description' => 'First Description',
        ]);

        $user->products()->attach($product);

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

    public function testsProductsCanBeRetrievedCorrectly()
    {
        $user = factory(User::class)->create();
        $token = $user->generateToken();
        $headers = ['Authorization' => "Bearer $token"];
        $product = factory(Product::class)->create();
        $user->products()->attach($product);

        $this->json('GET', '/api/products/' . $product->id, [], $headers)
            ->assertStatus(200)
            ->assertJson($product->toArray());
    }

    public function testsProductsCanBeAddedToAUserCorrectly()
    {
        $user = factory(User::class)->create();
        $token = $user->generateToken();
        $headers = ['Authorization' => "Bearer $token"];
        $product = factory(Product::class)->create();

        $this->json('POST', '/api/products/' . $product->id.'/attach', [], $headers)
            ->assertStatus(201)
            ->assertJson($product->toArray());
    }

    public function testsProductsCannotBeAddedToAUserWhenUserAlreadyHasProduct()
    {
        $user = factory(User::class)->create();
        $token = $user->generateToken();
        $headers = ['Authorization' => "Bearer $token"];
        $product = factory(Product::class)->create();
        $user->products()->attach($product);

        $this->json('POST', '/api/products/' . $product->id.'/attach', [], $headers)
            ->assertStatus(403);
    }

    public function testsProductsCanBeRemovedFromAUserCorrectly()
    {
        $user = factory(User::class)->create();
        $token = $user->generateToken();
        $headers = ['Authorization' => "Bearer $token"];
        $product = factory(Product::class)->create();
        $user->products()->attach($product);
        $this->assertTrue($user->products->contains($product));

        $this->json('DELETE', '/api/products/' . $product->id.'/detach', [], $headers)->assertStatus(200);
        $this->assertFalse($user->fresh()->products->contains($product));
    }

    public function testsProductsCannotRemoveFromAUserThatDoesntHaveProduct()
    {
        $user = factory(User::class)->create();
        $token = $user->generateToken();
        $headers = ['Authorization' => "Bearer $token"];
        $product = factory(Product::class)->create();
        $this->assertFalse($user->products->contains($product));

        $this->json('DELETE', '/api/products/' . $product->id.'/detach', [], $headers)
            ->assertStatus(403);
    }
}
