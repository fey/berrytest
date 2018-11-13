<?php

namespace Utilities;

function clean($value = '')
{
    $value = trim($value);
    // $value = stripslashes($value);
    // $value = strip_tags($value);
    $value = htmlspecialchars($value);

    return $value;
}
function rus2translit($string)
{
    $converter = [
        'а' => 'a',   'б' => 'b',   'в' => 'v',
        'г' => 'g',   'д' => 'd',   'е' => 'e',
        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
        'и' => 'i',   'й' => 'y',   'к' => 'k',
        'л' => 'l',   'м' => 'm',   'н' => 'n',
        'о' => 'o',   'п' => 'p',   'р' => 'r',
        'с' => 's',   'т' => 't',   'у' => 'u',
        'ф' => 'f',   'х' => 'h',   'ц' => 'c',
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
        'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

        'А' => 'A',   'Б' => 'B',   'В' => 'V',
        'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
        'И' => 'I',   'Й' => 'Y',   'К' => 'K',
        'Л' => 'L',   'М' => 'M',   'Н' => 'N',
        'О' => 'O',   'П' => 'P',   'Р' => 'R',
        'С' => 'S',   'Т' => 'T',   'У' => 'U',
        'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
        'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
    ];

    return strtr($string, $converter);
}
function str2url($str)
{
    // переводим в транслит
    $str = rus2translit($str);
    // в нижний регистр
    $str = strtolower($str);
    // заменям все ненужное нам на "-"
    $str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);
    // удаляем начальные и конечные '-'
    $str = trim($str, '-');

    return $str;
}
function isPaged(int $num, int $max): bool
{
    return $num <= floor($max / 5) && $num > 1;
}
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
function echoComments($comments, $parent = 0)
{
    foreach ($comments as $comment) {
        include '../resources/views/comment.phtml';
        if (isset($comment['children'])) {
            echoComments($comment['children'], $parent + 1);
        }
        echo '</div>';
    }
}
