<?php

namespace App;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../vendor/autoload.php';
use function App\Renderer\render;
use Db\Repository;

$articles = new Repository('articles');
$app = new Application();
$authors = new Repository('authors');
$comments = new Repository('comments');
$sessid = session_id();
$app->get('/session', function ($request) use ($articles, $authors) {
    $sessionId = $_COOKIE['PHPSESSID'];
    $author = $authors->findBy('ssid', $_COOKIE['PHPSESSID']);

    return response(var_dump(session_id(), $_SESSION, $sessionId, $_COOKIE, $author));
});

$app->get('/', function ($request, $attributes) use ($articles, $authors) {
    $articlesPerPage = array_map(function ($item) use ($authors) {
        $author = $authors->findBy('id', $item['author_id']);
        $item['author'] = $author['name'];

        return $item;
    }, $articles->getPage());

    $pages = ['current' => 1, 'count' => $articles->count()];

    return response(render('index', [
        'title' => 'Главная страница',
        'articles' => $articlesPerPage,
        'pages' => $pages,
        ]));
});
$app->get('/page/:page', function ($request, $attributes) use ($articles, $authors) {
    $articlesPerPage = array_map(function ($item) use ($authors) {
        $author = $authors->findBy('id', $item['author_id']);
        $item['author'] = $author['name'];

        return $item;
    }, $articles->getPage($attributes['page']));
    $pages = ['current' => $attributes['page'], 'count' => $articles->count()];
    if ($pages['current'] < 2) {
        return response()->redirect('/');
    }

    return response(render('article', [
        'articles' => $articlesPerPage,
        'pages' => $pages,
        'title' => "Новости, страница {$attributes['page']}",
        ]));
});

$app->get('/new', function ($request) {
    return response(render('new', ['title' => 'Добавить новость']));
});
$app->get('/article/:id', function ($request, $attributes) use ($articles, $authors, $comments) {
    $articleId = (int) $attributes['id'];
    $article = $articles->findBy('id', $articleId);
    $article['author'] = $authors->findBy('id', $article['author_id'])['name'];
    $comments = array_filter($comments->all(), function ($item) use ($articleId) {
        return $item['article_id'] === $articleId;
    });
    $commentsTree = \Utilities\buildTree($comments);

    if ($article) {
        return response(render('show.article', [
            'title' => $article['title'],
            'article' => $article,
            'comments' => $commentsTree,
            'countComments' => count($comments),
            ]));
    }
});

$app->post('/articles', function ($request) use ($articles, $authors) {
    $formData = array_map('\Utilities\clean', $request->getQueryParam('article'));
    $errors = array_filter($formData, function ($value) {
        return empty($value);
    });
    $_SESSION['author'] = $formData['author'];
    if ($errors) {
        return response(render('new', ['title' => 'Добавить новость', 'formData' => $formData, 'errors' => $errors]));
    }
    if ($authors->findBy('ssid', $_COOKIE['PHPSESSID'])) {
        $authors->update(['name' => $formData['author']], 'ssid', $_COOKIE['PHPSESSID']);
    } else {
        $authors->insert(['name' => $formData['author'], 'ssid' => $_COOKIE['PHPSESSID']]);
    }
    $author = $authors->findBy('ssid', $_COOKIE['PHPSESSID']);
    $articles->insert([
        'description' => $formData['description'],
        'text' => str_replace(PHP_EOL, '</br>', $formData['text']),
        'author_id' => $author['id'],
        'title' => $formData['title'],
    ]);

    return response()->redirect('/');
});

$app->delete('/articles', function ($request) use ($articles) {
    $articles->truncate('articles');

    return response()->redirect('/');
});

$app->run();
