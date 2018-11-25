<?php

namespace Db;

class CommentManager
{
    private $articleId;

    public function __construct($articleId)
    {
        $this->repo = new Repository('comments');
        $this->articleId = $articleId;
    }

    public function save($data)
    {
        $data = $this->sanitize($data);
        $prepareData = [
            'body' => str_replace(PHP_EOL, '</br>', $data['body']),
            'author' => $data['author'],
            'parent_id' => $data['parent_id'],
            'article_id' => $this->articleId,
        ];

        return $this->repo->insert($prepareData);
    }

    public function validate($data)
    {
        return array_filter($this->sanitize($data), function ($item) {
            return $item === '';
        });
    }

    public function sanitize($data)
    {
        return array_map('\Utilities\clean', $data);
    }

    public function count()
    {
        return count($this->getComments());
    }

    public function getComments()
    {
        $comments = array_filter($this->repo->all(), function ($item) {
            return $item['article_id'] === $this->articleId;
        });

        return array_map(function ($item) {
            return $this->makeComment($item);
        }, $comments);
    }

    public function getTree()
    {
        $comments = \Utilities\buildTree($this->getComments());

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
