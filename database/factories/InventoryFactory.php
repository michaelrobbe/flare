<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use App\Flare\Models\Inventory;


/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Inventory::class, function (Faker $faker) {
    return [
        'character_id' => null,
    ];
});