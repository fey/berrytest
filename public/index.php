<?php

namespace App;

$autoloadPath1 = __DIR__.'/../../../autoload.php';
$autoloadPath2 = __DIR__.'/../vendor/autoload.php';
require_once (file_exists($autoloadPath1)) ? $autoloadPath1 : $autoloadPath2;

use function App\Renderer\render;

$app = new Application();

$app->get('/', function () {
    $paginator = ['current' => 1, 'count' => $this->articles->count()];
    return response(render('index', [
        'title' => 'Главная страница',
        'articles' => $this->articles->getPage(1),
        'pages' => $paginator,
        ]));
});
$app->get('/new', function () {
    return response(render('new', ['title' => 'Добавить новость']));
});
$app->get('/page/:page', function ($request, $attributes) {
    $current = (int) $attributes['page'];
    if ($current < 2) {
        return response()->redirect('/');
    }
    $paginator = ['current' => $attributes['page'], 'count' => $this->articles->count()];
    return response(render('index', [
        'articles' => $this->articles->getPage($current),
        'pages' => $paginator,
        'title' => "Новости, страница {$current}",
        ]));
});

$app->get('/article/:id', function ($request, $attributes) {
    $id = (int) $attributes['id'];

    $article = $this->articles->getById($id);

    return response(render('show.article', [
        'title' => $article->getTitle(),
        'article' => $article,
        'comments' => [],
        'countComments' => 0,
        ]));
});

$app->post('/articles', function ($request) {
    $formData = $request->getQueryParam('article');
    $this->articles->insert($formData);
    return response()->redirect('/'); 
    $this->articles->insert($formData);
    $errors = $manager->validate($formData);
    if (!empty($errors)) {
        return response(render('new', [
            'title' => 'Добавить новость',
            'formData' => $formData,
            'errors' => $errors, ]))->withStatus(400);
    } else {
    $_SESSION['author'] = $formData['author'];
    $this->articles->save($formData);
    return response(render('new', [
        'title' => 'Добавить новость',
        'formData' => $formData,
        'errors' => $errors,
        ]))->withStatus(400);
    }
    return response()->redirect('/');
});

// create Comment
$app->post('/comments/:articleId/:parentId', function ($request, $attributes) {
    $articleId = (int) $attributes['article'];
    $parentId  = (int) $attributes['parentId'] ?: 0;
    $article = $this->articles->getById($articleId);
    $formData = $request->getQueryParam('comment');
    // $errors = $commentManager->validate($formData);
    if ($errors) {
        return response(json_encode($errors))->withStatus(400);

        return response(render('show.article', [
            'title' => 'Добавить новость',
            'formData' => $formData,
            'errors' => $errors,
            'article' => $article,
            'comments' => $commentManager->getTree(),
            'countComments' => 0,
            ]));
    }
        $_SESSION['author'] = $formData['author'];
        $lastInsertId = $this->comments->save($formData);

        return response(json_encode($errors))->withStatus(400);

    $newComment = json_encode($this->comments->getById($lastInsertId));

    return ($request->getHeader('HTTP_X_REQUESTED_WITH')) 
    ? response($newComment)
    : response()->redirect("/article/{$attributes['id']}");
});

$app->run();
