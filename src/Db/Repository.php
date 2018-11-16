<?php

namespace Db;

class Repository
{
    protected $pdo;
    private $table;

    public function __construct($table)
    {
        $this->pdo = (new Connection())->get();

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

    public function getPage($page = 1, $limit = 5)
    {
        $sql = "select * from {$this->table} ORDER BY created_at DESC LIMIT ? OFFSET ?";
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
}
