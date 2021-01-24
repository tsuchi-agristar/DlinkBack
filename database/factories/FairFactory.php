<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Fair;
use Faker\Generator as Faker;

$factory->define(Fair::class, function (Faker $faker) use ($factory) {
    return [
        "fair_id" => $faker->uuid,
        "hospital_id" => $factory->create(App\Models\Hospital::class)->hospital_id,
        "fair_status" => $faker->randomElement(config('const.FAIR_STATUS')),
        "plan_start_at" => $faker->dateTime($max = 'now', $timezone = null),
        "plan_end_at" => $faker->dateTime($max = 'now', $timezone = null)
    ];
});
