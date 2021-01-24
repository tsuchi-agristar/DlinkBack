<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Estimate;
use Faker\Generator as Faker;

$factory->define(Estimate::class, function (Faker $faker) use ($factory) {
    return [
        "estimate_id" => $faker->uuid,
        "event_id" => $factory->create(App\Models\OnlineEvent::class)->event_id,
        "estimate_status" => $faker->randomElement(config('const.ESTIMATE_STATUS')),
        "regular_price" => $faker->randomElement([1000,5000,10000,20000]),
        "discount_price" => $faker->randomElement([100,500,1000,2000]),
        "estimate_price" => $faker->randomElement([11000,5500,11000,22000])
    ];
});
