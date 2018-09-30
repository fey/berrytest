<?php

namespace App\Controller;

use function App\response;

class Test
{
    public function index()
    {
        return response('test');
    }

    public function getUser($meta, $params, $attributes, $cookies, $session)
    {
        return response(var_dump($params, $attributes));
    }
}
