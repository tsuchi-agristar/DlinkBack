<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\School;
use Faker\Generator as Faker;

$factory->define(School::class, function (Faker $faker) use ($factory) {
    return [
        'school_id' => $factory->create(App\Models\Organization::class)->organization_id,
        'school_type' => $faker->randomElement(config('const.SCHOOL_TYPE')),
        'student_number' => $faker->randomElement([100,200,300,400,500,600,700]),
        'scholarship_request' => $faker->randomElement([true, false]),
        'internship_request' => $faker->randomElement([true, false]),
        'practice_request' => $faker->randomElement([true, false]),
    ];
});
