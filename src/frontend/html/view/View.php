<?php

namespace suql\frontend\html\view;

class View
{
    public static function render(string $component, array $properties = [], $useJs = true)
    {
        $instance = new $component($properties);
        $instance->useJs($useJs);
        $view = $instance->render();
        return $view;
    }
}