<?php

namespace App;

require_once '../vendor/autoload.php';
use function App\Renderer\render;
use Db\Repository;

$repo = new Repository('articles');
$app = new Application();
// $meta, $params, $attributes, $cookies, $session
$app->get('/debug', function ($request) use ($repo) {
    return response(var_dump($request->getQueryParams()));
});

$app->get('/', function ($request, $attributes) use ($repo) {
    $articles = $repo->getPage();
    $pages = ['current' => 1, 'count' => $repo->count()];

    return response(render('common/index', ['title' => 'Главная страница', 'articles' => $articles, 'pages' => $pages]));
});

$app->get('/page/:page', function ($request, $attributes) use ($repo) {
    $pages = ['current' => $attributes['page'], 'count' => $repo->count()];
    $articles = $repo->getPage($pages['current']);
    if ($pages['current'] <= 1) {
        return response()->redirect('/');
    }

    return response(render('common/index', ['title' => 'Главная страница', 'articles' => $articles, 'pages' => $pages]));
});

$app->get('/new', function ($request) {
    return response(render('articles/new'));
});
$app->get('/article/:id', function ($request, $attributes) use ($repo) {
    $article = $repo->findBy('id', $attributes['id']);
    if ($article) {
        return response(render('articles/index', ['article' => $article]));
    }
});

$app->post('/articles', function ($request) use ($repo) {
    $formData = array_map('\Utilities\clean', $request->getQueryParam('article'));
    // $articles->insert

    $repo->insert([
        'description' => $formData['description'],
        'text' => str_replace(PHP_EOL, '</br>', $formData['text']),
        'author_id' => 1,
    ]);

    return response()->redirect('/');
});

$app->delete('/articles', function ($request) use ($repo) {
    $repo->truncate('articles');

    return response()->redirect('/');
});

$app->run();
