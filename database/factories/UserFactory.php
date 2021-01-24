<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use Faker\Generator as Faker;

$factory->define(User::class, function (Faker $faker) use ($factory) {
    return [
        'user_id' => $faker->uuid,
        'organization_id' => $factory->create(App\Models\Organization::class)->organization_id,
        'mail_address' => $faker->email,
        'account_name' => $faker->userName,
        'password' => $faker->password,
    ];
});
