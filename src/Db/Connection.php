<?php

namespace  Db;

use PDO;

class Connection
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = new PDO('pgsql:host=tantor.db.elephantsql.com;dbname=fihqhtuc;', 'fihqhtuc', 'aGGcjL1UX_h26cyWrIdwWtFdnYijcH44', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_PERSISTENT => true,
        ]);
    }

    public function get()
    {
        return $this->pdo;
    }

    public function getConfig()
    {
        return parse_ini_file(__DIR__.DIRECTORY_SEPARATOR.'../config.ini');
    }
}
