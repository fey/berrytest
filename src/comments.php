<?php
    $comments = [
        [
            'id' => 1,
            'author_id' => '22',
            'article_id' => '68',
            'parent_id' => 0,
            'body' => 'текст комментария 1',
        ],
        [
            'id' => 2,
            'author_id' => 22,
            'article_id' => 68,
            'parent_id' => 1,
            'body' => 'текст комментария 2',
        ],
        [
            'id' => 3,
            'author_id' => 22,
            'article_id' => 68,
            'parent_id' => 2,
            'body' => 'текст комментария 3',
        ],
        [
            'id' => 3,
            'author_id' => 22,
            'article_id' => 68,
            'parent_id' => 2,
            'body' => 'текст комментария 3',
        ],
        [
            'id' => 4,
            'author_id' => 22,
            'article_id' => 68,
            'parent_id' => 2,
            'body' => 'текст комментария 3',
        ],
        [
            'id' => 7,
            'author_id' => 22,
            'article_id' => 68,
            'parent_id' => 2,
            'body' => 'текст комментария 3',
        ],
        [
            'id' => 7,
            'author_id' => 22,
            'article_id' => 68,
            'parent_id' => 0,
            'body' => 'текст комментария 3',
        ],
    ];
    function buildTree($flat)
    {
        if (empty($flat)) {
            return [];
        }
        $grouped = array_reduce($flat, function ($acc, $item) {
            $acc[$item['parent_id']][] = $item;

            return $acc;
        }, []);

        $fnBuilder = function ($siblings) use (&$fnBuilder, $grouped) {
            foreach ($siblings as $key => $sibling) {
                $id = $sibling['id'];
                if (isset($grouped[$id])) {
                    $sibling['children'] = $fnBuilder($grouped[$id]);
                }
                $siblings[$key] = $sibling;
            }

            return $siblings;
        };

        $tree = $fnBuilder($grouped[0]);

        return $tree;
    }
    $commentsTree = buildTree($comments);

    function echoComments($comments, $parent = 0)
    {
        $printTab = str_repeat("\t", $parent);

        foreach ($comments as $comment) {
            echo $printTab.'comment text with id'.$comment['id'].PHP_EOL;
            if (isset($comment['children'])) {
                echoComments($comment['children'], $parent + 1);
            }
        }
    }
