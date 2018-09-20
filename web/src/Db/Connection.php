<?php

namespace  Db;

class Connection
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = new PDO('pgsql:host=berrytestdb;dbname=berrytest;', 'rootuser', 'rootpass');
    }
}
