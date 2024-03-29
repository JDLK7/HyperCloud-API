<?php

use Illuminate\Support\Facades\Storage;

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
        'space' => 0,
        'user_id' => function() {
            return factory(App\User::class)->create()->id;
        }
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Folder::class, function (Faker\Generator $faker) {

    $name = $faker->word;
    $path = "test/$name/";

    return [
        'name' => $name,
        'path' => $path,
        'size' => 4096,
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
    $path = "test/$name";

    Storage::disk('files')->put($path, $faker->text);

    return [
        'name' => $name,
        'path' => $path,
        'size' => 0,
        'extension' => $extension,
        'type' => 'archive',
        'account_id' => null,
        'group_id' => null,
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Suscription::class, function (Faker\Generator $faker) {
    $spaceOffer = $faker->numberBetween(10737418240, 1099511627776);
    $price = intval($spaceOffer * 5E-11);

    return [
        'name' => ucfirst($faker->word),
        'spaceOffer' => $spaceOffer,
        'price' => $price,
        'description' => $faker->sentence,
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Group::class, function (Faker\Generator $faker) {

    $name = $faker->word;
    
    return [
        'name' => $name,
        'description' => $faker->sentence,
        'path' => "groups/$name/",
        'iconPath' => '',
    ];
});
