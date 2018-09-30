<?php

namespace App;

class Session implements SessionInterface
{
    // BEGIN (write your solution here)
    public function start()
    {
        session_start();
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
        // return $this;
    }

    public function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public function destroy()
    {
        session_unset();
        session_destroy();
    }

    // END
}
