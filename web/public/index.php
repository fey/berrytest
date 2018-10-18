<?php

namespace App;

require_once '../vendor/autoload.php';
use function App\Renderer\render;
use Db\Repository;

$repo = new Repository('articles');
$app = new Application();
// $meta, $params, $attributes, $cookies, $session

$app->get('/', function ($meta, $params, $attributes, $cookies, $session) use ($repo) {
    $articles = $repo->all();

    return response(render('index', ['articles' => $articles]));
});
$app->get('/new', function ($meta, $params, $attributes, $cookies, $session) {
    return response(render('articles/new'));
});
$app->get('/article/:id', function ($meta, $params, $attributes, $cookies, $session) use ($repo) {
    $article = $repo->findBy('id', $attributes['id']);
    if ($article) {
        return response(var_dump($article));

        return response(render('articles/index', ['article' => $article]));
    }
});

$app->post('/articles', function ($meta, $params, $attributes, $cookies, $session) use ($repo) {
    $formData = array_map('\clean', $params['article']);
    // $articles->insert

    $repo->insert([
        'description' => $formData['description'],
        'text' => str_replace(PHP_EOL, '</br>', $formData['text']),
        'author_id' => 1,
    ]);

    return response()->redirect('/');
});

$app->delete('/articles', function ($meta, $params, $attributes, $cookies, $session) use ($repo) {
    $truncate = $params['truncate'] ?? false;
    if ($truncate) {
        $repo->truncate('articles');
    }

    return response()->redirect('/');
});

$app->run();
