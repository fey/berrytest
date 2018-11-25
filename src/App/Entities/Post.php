<?php

namespace App\Entities;

class Post
{
    private $fields = [];

    public function __construct($data)
    {
        $this->fields = $data;
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

    public function getDescription()
    {
        return $this->fields['description'];
    }

    public function getTextbody()
    {
        return $this->fields['text'];
    }

    public function getTitle()
    {
        return $this->fields['title'];
    }

    public function __set($key, $value)
    {
        return null;
    }

    public function getAuthor()
    {
        return $this->fields['author'];
    }
}
