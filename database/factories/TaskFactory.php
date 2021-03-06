<?php

use App\User;
use Faker\Generator as Faker;

$factory->define(App\Task::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'name' => $faker->sentence
    ];
});
