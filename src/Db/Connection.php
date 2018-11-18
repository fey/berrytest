<?php

namespace  Db;

use PDO;

class Connection
{
    private $pdo;

    public function __construct()
    {
        $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_PERSISTENT => true, ];
        $db = parse_url(getenv('DATABASE_URL') ?: 'postgres://fihqhtuc:aGGcjL1UX_h26cyWrIdwWtFdnYijcH44@tantor.db.elephantsql.com:5432/fihqhtuc');
        if ($db) {
            $this->pdo = new PDO('pgsql:'.sprintf(
                'host=%s;port=%s;user=%s;password=%s;dbname=%s',
                $db['host'],
                $db['port'],
                $db['user'],
                $db['pass'],
                ltrim($db['path'], '/')
            ), $options);
        } else {
            // $this->pdo = new PDO(
            //     'pgsql:host=tantor.db.elephantsql.com;dbname=fihqhtuc;',
            //     'fihqhtuc',
            //     'aGGcjL1UX_h26cyWrIdwWtFdnYijcH44',
            //     $options
            // );
        }
    }

    public function get()
    {
        return $this->pdo;
    }
}
