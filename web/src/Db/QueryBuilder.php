<?php

namespace Db;

class Query
{
    private $pdo;
    private $where = [];

    public function __construct($pdo, $table, $where = [])
    {
        $this->pdo = $pdo;
        $this->table = $table;
        $this->where = $where;
    }

    public function where($key, $value)
    {
        $where = [$key => $value];

        return $this->getClone($where);
    }

    public function all()
    {
        return $this->pdo->query($this->toSql())->fetchAll();
    }

    public function toSql()
    {
        $sqlParts = [];
        $sqlParts[] = "SELECT * FROM {$this->table}";
        if ($this->where) {
            $where = implode(' AND ', array_map(function ($key, $value) {
                $quotedValue = $this->pdo->quote($value);

                return "$key = $quotedValue";
            }, array_keys($this->where), $this->where));
            $sqlParts[] = "WHERE $where";
        }

        return implode(' ', $sqlParts);
    }

    private function getClone($where)
    {
        $mergedData = array_merge($this->where, $where);

        return new self($this->pdo, $this->table, $mergedData);
    }
}
