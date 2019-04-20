<?php

namespace App\Template;
 
function render($template, $variables)
{
    extract($variables);
    ob_start();
    $templatesPath = \Utilities\getRootDir() . "/resources/views/common/";
    include $templatesPath . "head.phtml";
    include $template;
    include $templatesPath . "foot.phtml";
    return ob_get_clean();
}
