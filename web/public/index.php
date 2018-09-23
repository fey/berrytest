<?php

namespace App;

error_reporting(E_ALL & ~E_NOTICE);
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__.$url['path'];
    if (is_file($file)) {
        return false;
    }
}
var_dump($_SERVER);
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
