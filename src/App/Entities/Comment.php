<?php

namespace App\Entities;

class Comment
{
    private $fields = [];

    public function __set($key, $value)
    {
        $this->$fields[$key] = $value;
    }

    public function __construct($data)
    {
        $this->fields = $data;
    }

    public function getAuthor()
    {
        return $this->fields['author'];
    }

    public function getTextbody()
    {
        return $this->fields['body'];
    }

    public function getCreatedAt(string $format = 'Y-m-d H:i', string $timezome = 'Europe/Moscow')
    {
        $datetime = new \DateTime($this->fields['created_at']);
        $datetime->setTimezone(new \DateTimeZone($timezome));

        return $datetime->format($format);
    }

    public function getId()
    {
        return $this->fields['id'];
    }

    public function getParentId()
    {
        return $this->fields['parent_id'];
    }

    public function getPostId()
    {
        return $this->fields['article_id'];
    }

    public function getChildren()
    {
        return $this->fields['children'] ?? null;
    }

    public function setChildren($data)
    {
        $this->fields['children'] = $data;
    }
}
