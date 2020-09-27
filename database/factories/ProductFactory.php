<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use App\Product;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    $name = $faker->words(3, true);
    return [
        'user_id' => factory(User::class),
        'name' => $name,
        'description' => $faker->sentences(2, true),
        'price' => $faker->randomNumber(4, $strict = false),
        'image' => Str::of($name)->slug('-').'.jpg',
    ];
});
