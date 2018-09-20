<?php

namespace App;

class App
{
    private $routes = [];

    public function run()
    {
        $uri = $this->getUri();
        $method = $this->getMethod();

        $response = $this->routes[$uri][$method];

        echo $response();
    }

    public function get($path, $handler)
    {
        $this->append($path, $handler, 'GET');
    }

    public function post($path, $handler)
    {
        $this->append($path, $handler, 'POST');
    }

    private function getUri()
    {
        return rtrim(strtolower($_SERVER['REQUEST_URI']), '/');
    }

    private function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    private function append($path, $handler, $method)
    {
        $methodUpperCase = strtoupper($method);
        $this->routes[$path][$methodUpperCase] = $handler;
    }
}
