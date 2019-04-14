<?php

namespace App;

$autoloadPath1 = __DIR__.'/../../../autoload.php';
$autoloadPath2 = __DIR__.'/../vendor/autoload.php';
if (file_exists($autoloadPath1)) {
    require_once $autoloadPath1;
} else {
    require_once $autoloadPath2;
}
use function App\Renderer\render;
use Db\Repository;
use Db\PostManager;
use Db\CommentManager;

$app = new Application();

$articles = new Repository('articles');
$comments = new Repository('comments');
$sessid = session_id();
$app->postManager = new PostManager();

$app->get('/', function ($request, $attributes) {
    $postManager = new PostManager();
    $articlesPerPage = $postManager->getPage();

    $pages = ['current' => 1, 'count' => $postManager->getCount()];

    return response(render('index', [
        'title' => 'Главная страница',
        'articles' => $articlesPerPage,
        'pages' => $pages,
        ]));
});
$app->get('/page/:page', function ($request, $attributes) {
    $page = (int) $attributes['page'];
    if ($page < 2) {
        return response()->redirect('/');
    }
    $postManager = new PostManager();
    $articlesPerPage = $postManager->getPage($page);
    $pages = ['current' => $attributes['page'], 'count' => $postManager->getCount()];

    return response(render('index', [
        'articles' => $articlesPerPage,
        'pages' => $pages,
        'title' => "Новости, страница {$page}",
        ]));
});
$app->get('/new', function ($request) {
    return response(render('new', ['title' => 'Добавить новость']));
});
$app->post('/articles', function ($request) use ($articles) {
    $formData = $request->getQueryParam('article');
    $manager = new PostManager();
    $errors = $manager->validate($formData);
    if (!empty($errors)) {
        return response(render('new', [
            'title' => 'Добавить новость',
            'formData' => $formData,
            'errors' => $errors, ]))->withStatus(400);
    }
    try {
        $_SESSION['author'] = $formData['author'];
        $manager->save($formData);
    } catch (\PDOException $e) {
        if ($e->getCode() === '22001') {
            $errors['db'] = 'Поля слишком длинные';
        }
        return response(render('new', [
            'title' => 'Добавить новость',
            'formData' => $formData,
            'errors' => $errors,
            ]))->withStatus(400);

        return response($e->getMessage());
    }

    return response()->redirect('/');
});

$app->post('/article/:id', function ($request, $attributes) {
    $id = (int) $attributes['id'];
    $commentManager = new CommentManager($id);
    $postManager = new PostManager();
    $article = $postManager->getById($id);
    $formData = $commentManager->sanitize($request->getQueryParam('comment'));
    $errors = $commentManager->validate($formData);
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
    try {
        $_SESSION['author'] = $formData['author'];
        $lastInsertId = $commentManager->save($formData);
    } catch (\PDOException $e) {
        $errors['db'] = $e->getMessage();
        return response(json_encode($errors))->withStatus(400);
    }

    $newComment = json_encode($commentManager->getById($lastInsertId));

    return ($request->getHeader('HTTP_X_REQUESTED_WITH')) ? response($newComment) :
    response()->redirect("/article/{$attributes['id']}");
});

$app->get('/article/:id', function ($request, $attributes) {
    $id = (int) $attributes['id'];
    $postManager = new PostManager();
    $commentManager = new CommentManager($id);
    $article = $postManager->getById($id);

    return response(render('show.article', [
        'title' => $article->getTitle(),
        'article' => $article,
        'comments' => $commentManager->getTree(),
        'countComments' => $commentManager->count(),
        ]));
});

$app->delete('/articles', function ($request) use ($articles) {
    $articles->truncate('articles');

    return response()->redirect('/');
});
$app->run();
