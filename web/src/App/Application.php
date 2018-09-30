<?php

namespace App;

class Application
{
    public $handlers = [];

    public function run()
    {
        $uri = $this->getUri();
        $method = $this->getMethod();
        foreach ($this->handlers as $item) {
            [$route, $handlerMethod, $handler] = $item;
            $preparedRoute = str_replace('/', '\/', $route);
            $matches = [];
            $isMatched = preg_match("/^$preparedRoute$/i", $uri, $matches);
            if ($method == $handlerMethod && $isMatched) {
                error_log("$method \t $uri");

                $attributes = array_filter($matches, function ($key) {
                    return !is_numeric($key);
                }, ARRAY_FILTER_USE_KEY);

                $meta = [
                    'method' => $method,
                    'uri' => $uri,
                    'headers' => getallheaders(),
                ];

                $session = new Session();

                $response = $handler($meta, array_merge($_GET, $_POST), $attributes, $_COOKIE, $session);
                http_response_code($response->getStatusCode());
                foreach ($response->getHeaderLines() as $header) {
                    header($header);
                }

                echo $response->getBody();

                return;
            }
        }
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

    private function getUri()
    {
        $url = strtolower(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

        return rtrim($url, '/');
    }

    private function getMethod()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && array_key_exists('_method', $_POST)) {
            $method = strtoupper($_POST['_method']);
        } else {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        return $method;
    }

    private function append($method, $route, $handler)
    {
        $updatedRoute = preg_replace('/:(\w+)/', '(?<$1>[\w-]+)', $route);
        $this->handlers[] = [$updatedRoute, $method, $handler];
    }
}
