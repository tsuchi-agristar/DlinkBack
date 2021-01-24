<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Hospital;
use Faker\Generator as Faker;

$factory->define(Hospital::class, function (Faker $faker) use ($factory) {
    return [
        'hospital_id' => $factory->create(App\Models\Organization::class)->organization_id,
        'hospital_type' => $faker->randomElement(config('const.HOSPITAL_TYPE'))
    ];
});
