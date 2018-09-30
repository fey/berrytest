<?php

namespace App;

require_once '../vendor/autoload.php';
use function App\Renderer\render;

$app = new Application();

$app->get('', function ($meta, $params, $attributes, $cookies, $session) {
    $session->start();
    $nickname = $session->get('nickname');

    return response(render('index', ['nickname' => $nickname]));
});
$app->get('/test', '\App\Controller\Test::index');
$app->get('/test/:id', '\App\Controller\Test::getUser');
$app->run();
