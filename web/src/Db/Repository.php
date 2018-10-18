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
        $sql = "select * from {$this->table} ORDER BY created_at DESC";

        return $this->pdo->query($sql)->fetchAll();
    }

    public function insert($params)
    {
        $pdo = $this->pdo;

        $fields = implode(', ', array_keys($params));
        $values = implode(', ', array_map(function ($v) use ($pdo) {
            return $pdo->quote($v);
        }, array_values($params)));

        return $pdo->exec("insert into {$this->table} ($fields) values ($values)");
    }

    public function truncate($table)
    {
        $pdo = $this->pdo;

        return $pdo->exec("TRUNCATE $table CASCADE");
    }
}
