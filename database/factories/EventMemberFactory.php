<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\EventMember;
use Faker\Generator as Faker;

$factory->define(EventMember::class, function (Faker $faker) use ($factory) {
    return [
        "event_id" => $factory->create(App\Models\OnlineEvent::class)->event_id,
        "organization_id" => $factory->create(App\Models\Organization::class)->organization_id,
        "member_role" => $faker->randomElement(config('const.MEMBER_ROLE'))
    ];
});
