<?php

namespace App;

use function App\Renderer\render;
use Db\Repository;
use Db\PostManager;
use Db\CommentManager;

class Application
{
    public $handlers = [];
    public $request;
    public $session;
    public function __construct()
    {
        // $app->postManager = new PostManager();
        $this->articles = new PostManager('articles');
        $this->comments = new CommentManager('comments');
        $this->session = new Session();
    }
    public function run()
    {
        $this->session->start();
        $this->request = new Request();
        $uri = $this->request->getUri();
        $method = $this->request->getMethod();

        if (!empty($this->getHandlerItem())) {
            [$preparedRoute, $handlerMethod, $handler, $attributes] = $this->getHandlerItem();
            $handler = $handler->bindTo($this);
            $response = $handler($attributes);

            $response->sendResponseCode()->sendHeaders();
            echo $response->getBody();
        } else {
            echo response(render('404'))->withStatus(404)->getBody();
        }

        return;
    }
    public function get($route, $handler)
    {
        $this->append('GET', $route, $handler);
    }
    public function delete($route, $handler)
    {
        $this->append('DELETE', $route, $handler);
    }
    public function post($route, $handler)
    {
        $this->append('POST', $route, $handler);
    }
    private function append($method, $route, $handler)
    {
        $updatedRoute = preg_replace('/:(\w+)/', '(?<$1>[\w-]+)', $route);
        $this->handlers[] = [$updatedRoute, $method, $handler];
    }
    private function getHandlerItem()
    {
        $uri = $this->request->getUri();
        $method = $this->request->getMethod();

        return array_reduce($this->handlers, function ($acc, $item) use ($method, $uri) {
            [$route, $handlerMethod, $handler] = $item;
            $preparedRoute = str_replace('/', '\/', $route);
            $matches = [];
            $isMatched = preg_match("/^$preparedRoute$/i", $uri, $matches);
            $item[] = $this->parseAttributes($matches);

            return $method == $handlerMethod && $isMatched ? $item : $acc;
        }, []);
    }
    private function parseAttributes($matches)
    {
        return array_filter($matches, function ($key) {
            return !is_numeric($key);
        }, ARRAY_FILTER_USE_KEY);
    }
}
