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
    private $value;
    /**
     * @var array пути в шаблоне
     */
    private $pathList = [];
    /**
     * Constructor
     * @param string $name имя переменной
     */
    function __construct($name)
    {
        $this->name = $name;
    }
    /**
     * Получает имя переменной
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
     * Получает значение
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
     * Добавляет путь
     */
    public function addPath(Path $path)
    {
        $this->pathList[] = $path;
    }
}