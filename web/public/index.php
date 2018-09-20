<?php

namespace App;

error_reporting(E_ALL & ~E_NOTICE);
require_once __DIR__.'/../vendor/autoload.php';

$app = new App();

$app->get('/companies', function () {
    return 'companies list';
});

$app->post('/companies', function () {
    return 'company was created';
});

$app->get('/', function () {
    return 'Index Page';
});

$app->get('/about', function () {
    return 'About';
});
$app->run();
