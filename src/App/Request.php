<?php

namespace App;

class Request
{
    private $uri;
    private $headers;
    private $cookies;
    private $queryParams;

    public function __construct()
    {
        $this->uri = $this->getUri();
        $this->headers = $this->getAllHeaders();
        $this->queryParams = array_merge($_GET, $_POST);
    }

    public function getQueryParams()
    {
        return $this->queryParams;
    }

    public function getQueryParam($param, $default = null)
    {
        return $this->queryParams[$param] ?? $default;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getHeader($header, $default = null)
    {
        return $this->headers[$header] ?? null;
    }

    public function getUri()
    {
        $url = strtolower(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

        return trim($url);
    }

    public function getMethod()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && array_key_exists('_method', $_POST)) {
            $method = strtoupper($_POST['_method']);
        } else {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        return $method;
    }

    public function getAllHeaders()
    {
        if (!function_exists('getallheaders')) {
            $headers = $this->getHeaders();
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }

            return $headers;
        } else {
            return \getallheaders();
        }
    }
}
