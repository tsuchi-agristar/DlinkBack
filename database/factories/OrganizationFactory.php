<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Organization;
use Faker\Generator as Faker;

$factory->define(Organization::class, function (Faker $faker) {
    return [
        'organization_id' => $faker->uuid,
        'organization_type' => $faker->randomElement([2,3]),
        'organization_name' => "〇〇病院学校",
        'organization_name_kana' => "〇〇ビョウインガッコウ",
        'prefecture' => $faker->prefecture,
        'city' => $faker->city,
        'address' => $faker->streetAddress,
        'homepage' => $faker->url,
        'dummy' => $faker->randomElement([true,false]),
    ];
});
