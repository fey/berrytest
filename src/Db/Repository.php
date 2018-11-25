<?php

namespace Db;

use PDO;

class Repository
{
    private $pdo;
    private $table;

    public function __construct($table)
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
        $this->pdo->setAttribute(PDO::ATTR_PERSISTENT, true);

        $this->table = $table;
    }

    public function findBy($column, $value)
    {
        $sql = "select * from {$this->table} where {$column} = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$value]);

        return $stmt->fetch();
    }

    public function all()
    {
        // $sql = "select * from {$this->table} ORDER BY created_at DESC";
        $sql = "select * from {$this->table}";

        return $this->pdo->query($sql)->fetchAll();
    }

    public function getPage($page = 1, $column = 'created_at', $sort = 'DESC', $limit = 5)
    {
        $sql = "select * from {$this->table} ORDER BY {$column} {$sort} LIMIT ? OFFSET ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$limit, ($page - 1) * $limit]);

        return $stmt->fetchAll();
    }

    public function count()
    {
        return $this->pdo->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
    }

    public function insert($params)
    {
        $pdo = $this->pdo;

        $fields = implode(', ', array_keys($params));
        $values = implode(', ', array_map(function ($v) use ($pdo) {
            return $pdo->quote($v);
        }, array_values($params)));
        $sql = "INSERT INTO {$this->table} ($fields) VALUES ($values)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $pdo->lastInsertId();
    }

    public function update($params, $whereColumn, $whereValue)
    {
        $pdo = $this->pdo;
        $whereValue = $pdo->quote($whereValue);
        $values = implode(', ', array_map(function ($v, $k) use ($pdo) {
            $preparedV = $pdo->quote($v);

            return "{$k} = $preparedV";
        }, array_values($params), array_keys($params)));

        return $pdo->exec("UPDATE {$this->table} SET {$values} WHERE {$whereColumn} = {$whereValue}");
    }

    public function truncate($table)
    {
        return $this->pdo->exec("TRUNCATE $table CASCADE");
    }

    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }
}
