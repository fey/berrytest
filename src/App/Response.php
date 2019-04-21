<?php

namespace App;

function response($body = null)
{
    return new Response($body);
}

class Response implements ResponseInterface
{
    protected $headers = [];
    protected $status = 200;
    protected $body;

    public function __construct($body)
    {
        $this->body = $body;
    }

    public function redirect($url)
    {
        $this->headers['Location'] = $url;

        return $this->withStatus(302);
    }

    public function withStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function format($format)
    {
        switch ($format) {
            case 'json':
                $this->headers['Content-Type'] = 'application/json';
                $this->body = json_encode($this->body);
                $this->headers['Content-Length'] = mb_strlen($this->body);
        }

        return $this;
    }

    public function getStatusCode()
    {
        return $this->status;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getHeaderLines()
    {
        return array_map(function ($key, $value) {
            return "$key: $value";
        }, array_keys($this->headers), $this->headers);
    }

    public function sendHeaders()
    {
        foreach ($this->getHeaderLines() as $header) {
            header($header);
        }

        return $this;
    }

    public function sendResponseCode()
    {
        http_response_code($this->getStatusCode());

        return $this;
    }

    public function withCookie($key, $value)
    {
        $this->cookies[$key] = $value;

        return $this;
    }

    public function getCookies()
    {
        return $this->cookies;
    }
}
