<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Folder::class, function (Faker\Generator $faker) {

    $name = $faker->word;
    $path = "files/test/$name/";

    @mkdir(base_path('files/test/'));
    mkdir(base_path($path), 755);

    return [
        'name' => $name,
        'path' => $path,
        'size' => 0,
        'extension' => null,
        'type' => 'folder',
        'account_id' => null,
        'group_id' => null,
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Archive::class, function (Faker\Generator $faker) {

    $extension = $faker->fileExtension;
    $name = $faker->word . '.' . $extension;
    $path = "files/test/$name";

    @mkdir(base_path('files/test/'));
    touch(base_path($path));

    return [
        'name' => $name,
        'path' => $path,
        'size' => 40,
        'extension' => $extension,
        'type' => 'archive',
        'account_id' => null,
        'group_id' => null,
    ];
});
