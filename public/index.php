<?php

namespace App;

use function App\Renderer\render;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Application();

$app->get('/', function () {
    $paginator = ['current' => 1, 'count' => $this->articles->count()];
    return response(render('index', [
        'title'     => 'Главная страница',
        'articles'  => $this->articles->getPage(1),
        'pages'     => $paginator,
    ]));
});
$app->get('/new', function () {
    return response(render('articles/new', [
        'title'  => 'Добавить новость',
        'errors' => [],
        'author' => $this->session->get('author')
    ]));
});
$app->get('/page/:page', function ($attributes) {
    $current = (int)$attributes['page'];
    if ($current < 2) {
        return response()->redirect('/');
    }
    $paginator = ['current' => $attributes['page'], 'count' => $this->articles->count()];
    return response(render('index', [
        'articles'  => $this->articles->getPage($current),
        'pages'     => $paginator,
        'title'     => "Новости, страница {$current}",
    ]));
});
$app->get('/article/:id', function ($attributes) {
    $id = (int)$attributes['id'];
    $article = $this->articles->getById($id);

    return response(render('articles/show', [
        'title'         => $article->getTitle(),
        'article'       => $article,
        'author'        => $this->session->get('author'),
        'comments'      => $this->comments->getTree($id),
        'countComments' => $this->comments->count($id),
        'errors'        => []
    ]));
});

$app->post('/articles', function () {
    $formData = $this->request->getQueryParam('article');
    $this->session->set('author', $formData['author']);
    $errors = $this->articles->validate($formData);
    if (!empty($errors)) {
        return response(render('articles/new', [
            'title'     => 'Добавить новость',
            'formData'  => $formData,
            'errors'    => $errors
        ]))->withStatus(400);
    }
    try {
        $newId = $this->articles->save($formData);
        return response()->redirect("/article/{$newId}");
    } catch (\Throwable $th) {
        error_log($th->getMessage(), 4);
        return response(render('articles/new', [
            'title'     => 'Добавить новость',
            'formData'  => $formData,
            'errors'    => ['db' => "Произошла ошибка соединения с базой"],
        ]))->withStatus(400);
    }
});

$app->post('/comments', function () {
    $formData = $this->request->getQueryParam('comment');
    $article  = $this->articles->getById((int)$formData['article_id']);
    $withAjax = (bool)$this->request->getHeader('X-Requested-With');
    $errors   = $this->comments->validate($formData);
    $this->session->set('author', $formData['author']);

    if ($errors) {
        return ($withAjax)
            ? response($errors)->format('json')->withStatus(400)
            : response(render('articles/show', [
                'title'         => $article->getTitle(),
                'article'       => $article,
                'author'        => $this->session->get('author'),
                'comments'      => $this->comments->getTree($article->getId()),
                'countComments' => $this->comments->count($article->getId()),
                'errors'        => $errors
            ]));
    }
    $id = $this->comments->save($formData);
    return ($withAjax)
        ? response($this->comments->getById($id))->format('json')
        : response()->redirect(sprintf("/article/%s#comment-%s", $formData['article_id'], $id));
});

$app->run();
