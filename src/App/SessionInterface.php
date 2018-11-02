<?php

namespace App;

interface SessionInterface
{
    public function start();

    public function set($key, $value);

    public function get($key, $default = null);

    public function destroy();
}
