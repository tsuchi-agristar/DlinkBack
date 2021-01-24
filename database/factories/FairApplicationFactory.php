<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\FairApplication;
use Faker\Generator as Faker;

$factory->define(FairApplication::class, function (Faker $faker) use ($factory) {
    return [
        "application_id" => $faker->uuid,
        "fair_id" => $factory->create(App\Models\Fair::class)->fair_id,
        "school_id" => $factory->create(App\Models\School::class)->school_id,
        "application_datetime" => $faker->dateTime($max = 'now', $timezone = null),
        "application_status" => $faker->randomElement(config('const.APPLICATION_STATUS')),
        "estimate_participant_number" => $faker->randomElement([1,3,5,7,9,10]),
        "format" => $faker->randomElement(config('const.EVENT_TYPE')),
        "comment" => "コメントコメントコメント"
    ];
});
