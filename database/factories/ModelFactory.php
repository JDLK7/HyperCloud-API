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
$factory->define(App\Account::class, function (Faker\Generator $faker) {

    $userName = $faker->userName;

    return [
        'userName' => $userName,
        'space' => $faker->numberBetween(0, 10),
        'path' => "files/users/$userName/",
        'avatarPath' => "",
        'user_id' => function() {
            return factory(App\User::class)->create()->id;
        }
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Folder::class, function (Faker\Generator $faker) {

    $name = $faker->word;
    $path = "files/test/$name/";

    @mkdir(base_path('files/test/'));

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

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Suscription::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
        'spaceOffer' => $faker->numberBetween(0, 1000),
        'price' => $faker->numberBetween(3, 25),
        'description' => $faker->sentence,
    ];
});
