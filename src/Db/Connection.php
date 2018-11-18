<?php

namespace  Db;

use PDO;

class Connection
{
    private $pdo;

    public function __construct()
    {
        $db = parse_url(getenv('DATABASE_URL'));
        $this->pdo = new PDO('pgsql:'.sprintf(
            'host=%s;port=%s;user=%s;password=%s;dbname=%s',
            $db['host'],
            $db['port'],
            $db['user'],
            $db['pass'],
            ltrim($db['path'], '/')
        ));
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
<<<<<<< HEAD
        $this->pdo->setAttribute(PDO::ATTR_PERSISTENT, true);
=======
        $this->pdo->setAttribute(PDO::ATTR_PERSISTENT, PDO::true);
>>>>>>> 0cee2c4c01dfe757b46775be823d3d462d6b6efa
    }

    public function get()
    {
        return $this->pdo;
    }
}
