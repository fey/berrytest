<?php

require_once '../vendor/autoload.php';

use GuzzleHttp\Client;

$faker = Faker\Factory::create('ru_RU');

// $faker->seed(1);
$guzzle = new Client([
    'base_uri' => 'http://berry-webserver',
]);

for ($i = 0; $i <= 36; ++$i) {
    $formParams = ['article' => ['author' => $faker->name, 'description' => "Заголовок + $i", 'text' => $faker->paragraphs(mt_rand(1, 10), true)]];
    $guzzle->post('/articles', ['form_params' => $formParams]);
}
