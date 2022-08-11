<?php

namespace suql\dom;

/**
 * Класс управления переменными в шаблоне
 * 
 * @author Yuriy Arutyunyan <sagittaracc@gmail.com>
 */
class Variable
{
    /**
     * @var string имя переменной
     */
    private $name;
    /**
     * @var mixed значение
     */
    public $value;
    /**
     * @var array пути в шаблоне
     */
    public $paths = [];
    /**
     * Constructor
     * @param string $name имя переменной
     */
    function __construct($name)
    {
        $this->name = $name;
    }
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * Устанавливает значение
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
    /**
     * Добавляет путь
     */
    public function addPath(Path $path)
    {
        $this->paths[$path->getPath()] = $path;
    }
    /**
     * 
     */
    public function getPath()
    {
        return $this->paths;
    }
}