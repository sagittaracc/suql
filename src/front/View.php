<?php

class View
{
    public static function render(string $component, array $properties = [])
    {
        $instance = new $component($properties);
        $view = $instance->render();
        return $view;
    }
}