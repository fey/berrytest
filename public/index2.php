<?php

require_once '../vendor/autoload.php';

use GuzzleHttp\Client;

$faker = Faker\Factory::create('ru_RU');

// $faker->seed(1);

for ($i = 0; $i <= 56; ++$i) {
    $guzzle = new Client(['base_uri' => 'http://localhost:8080/', 'cookies' => true]);
    $guzzle->get('/');
    $jar = new \GuzzleHttp\Cookie\CookieJar();
    $formParams = [
        'article' => [
            'author' => $faker->name,
            'description' => "Заголовок + $i",
            'text' => $faker->paragraphs(mt_rand(1, 10), true),
        ],
    ];
    $guzzle->request('POST', '/articles', ['form_params' => $formParams]);
}
