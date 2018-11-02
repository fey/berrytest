<?php

namespace App\Renderer;

use function App\Template\render as renderTemplate;

function render($filepath, $params = [])
{
    $templatepath = '../resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$filepath.'.phtml';

    return renderTemplate($templatepath, $params);
}
