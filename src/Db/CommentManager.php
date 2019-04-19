<?php

namespace Db;

class CommentManager
{
    const FIELDS_NAME = [
        'body'   => 'Комментарий',
        'author' => "Имя автора"
    ];

    public function __construct()
    {
        $this->repo = new Repository('comments');
    }

    public function save($data)
    {
        $data = $this->sanitize($data);
        $prepareData = [
            'body' => str_replace(PHP_EOL, '</br>', $data['body']),
            'author' => $data['author'],
            'parent_id' => $data['parent_id'],
            'article_id' => $data['article_id'],
        ];

        return $this->repo->insert($prepareData);
    }

    public function validate($data)
    {
        $emptys = (array_filter($this->sanitize($data), function ($item) {
            return $item === '';
        }));
        return array_reduce(array_keys($emptys), function ($acc, $item) {
            $acc[$item] = sprintf("%s не может быть пустым", self::FIELDS_NAME[$item]);
            return $acc;
        }, []);
    }

    public function sanitize($data)
    {
        return array_map('\Utilities\clean', $data);
    }

    public function count($articleId)
    {
        return count($this->getComments($articleId));
    }

    public function getComments($articleId)
    {
        $comments = array_filter($this->repo->all(), function ($item) use ($articleId) {
            return $item['article_id'] === $articleId;
        });

        return array_map(function ($item) {
            return $this->makeComment($item);
        }, $comments);
    }

    public function getTree($articleId)
    {
        $comments = \Utilities\buildTree($this->getComments($articleId));

        return $comments;
    }

    public function getByid($id)
    {
        return $this->repo->findBy('id', $id);
    }

    private function makeComment($data)
    {
        return new \App\Entities\Comment($data);
    }
}
