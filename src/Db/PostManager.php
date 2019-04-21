<?php

namespace Db;

use App\Entities\Post;

class PostManager
{
    private $repo;
    private $posts;
    const FIELDS_NAME = [
        'body' => "Текст статьи",
        'title' => "Заголовок",
        'author' => "Имя Автора",
        'description' => "Описание",
    ];
    const VARCHAR_LIMIT = 255;

    public function __construct()
    {
        $this->repo = new Repository('articles');
    }

    private function makePost($data)
    {
        return new Post($data);
    }

    public function getAll()
    {
        $data = $this->repo->All();
        $result = [];
        foreach ($data as $item) {
            $result[] = $this->makePost($item);
        }

        return $result;
    }

    public function getPage($page)
    {
        $data = $this->repo->getPage($page);
        $result = [];
        foreach ($data as $item) {
            $result[] = $this->makePost($item);
        }

        return $result;
    }

    public function getById(int $id)
    {
        return new Post($this->repo->findBy('id', $id));
    }

    public function save($data)
    {
        $data = $this->sanitize($data);
        $prepareData = [
            'description' => $data['description'],
            'body' => str_replace(PHP_EOL, '</br>', $data['body']),
            'title' => $data['title'],
            'author' => $data['author'],
        ];

        return $this->repo->insert($prepareData);
    }

    public function sanitize($data)
    {
        return array_map('\Utilities\clean', $data);
    }

    public function validate($data)
    {
        $sanitized = $this->sanitize($data);
        $errors = array_merge($this->checkEmpty($sanitized), $this->checkLong($sanitized));
        return $errors;
    }

    private function checkEmpty($data)
    {
        $emptys = array_filter($this->sanitize($data), function ($item) {
            return $item === '';
        });
        return array_reduce(array_keys($emptys), function ($acc, $item) {
            $acc[$item] = sprintf(
                "Поле %s не может быть пустым",
                self::FIELDS_NAME[$item]
            );
            return $acc;
        }, []);
    }
    private function checkLong($data)
    {
        $limitedKeys = array_intersect_key($data, array_flip(['author', 'title', 'description']));
        $longs = array_filter($limitedKeys, function ($item) {
            return (mb_strlen($item) > self::VARCHAR_LIMIT);
        });
        $acc = array_reduce(array_keys($longs), function ($acc, $item) {
            $acc[$item] = sprintf(
                "Длина поля %s не может быть больше %s", 
                self::FIELDS_NAME[$item], 
                self::VARCHAR_LIMIT
            );
            return $acc;
        }, []);

        return $acc;
    }
    public function count()
    {
        return $this->repo->count();
    }
}
