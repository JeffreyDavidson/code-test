<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use App\Subscription;
use Faker\Generator as Faker;

$factory->define(Subscription::class, function (Faker $faker) {
    $now = now();
    return [
        'user_id' => factory(User::class),
        'started_at' => $now,
        'ended_at' => $now->addDays(3),
    ];
});
