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
}