<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\OnlineEvent;
use Faker\Generator as Faker;

$factory->define(OnlineEvent::class, function (Faker $faker) use ($factory) {
    return [
        "event_id" => $faker->uuid,
        "fair_id" => $faker->randomElement([null, $factory->create(App\Models\Fair::class)->fair_id]),
        "event_type" => $faker->randomElement(config('const.EVENT_TYPE')),
        "event_status" => $faker->randomElement(config('const.EVENT_STATUS')),
        "channel_status" => $faker->randomElement(config('const.CHANNEL_STATUS')),
        "start_at" => $faker->dateTime($max = 'now', $timezone = null),
        "end_at" => $faker->dateTime($max = 'now', $timezone = null)

    ];
});
