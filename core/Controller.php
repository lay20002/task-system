<?php
class Controller
{
    protected function view(string $view, array $data = [], bool $useLayout = true)
    {
        extract($data);

        ob_start();
        require "views/{$view}.php";
        $content = ob_get_clean();

        if ($useLayout) {
            require "views/layout.php";
        } else {
            echo $content;
        }
    }
}
