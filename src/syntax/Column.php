<?php

namespace suql\syntax;

/**
 * Конфигурация поля модели
 * 
 * @author Yuriy Arutyunyan <sagittaracc@gmail.com>
 */
class Column
{
    /**
     * @var string имя поля
     */
    private $name;
    /**
     * @var string тип поля
     */
    private $type;
    /**
     * @var integer длина поля
     */
    private $length;
    /**
     * @var mixed значение по умолчанию
     */
    private $default;
    /**
     * Конструктор
     * @param string $name имя поля
     */
    function __construct($name)
    {
        $this->name = $name;
    }
    /**
     * Алиас конструктора
     * @param string $name имя поля
     */
    public static function create($name)
    {
        return new static($name);
    }
    /**
     * Устанавливает тип поля
     * @param string $type тип поля
     */
    public function setType($type)
    {
        $this->type = $type;
    }
    /**
     * Получает тип поля
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * Устанавливает длину поля
     * @param integer $length длина поля
     */
    public function setLength($length)
    {
        $this->length = $length;
    }
    /**
     * Получает длину поля
     * @return integer
     */
    public function getLength()
    {
        return $this->length;
    }
    /**
     * Задает значение по умолчанию для поля
     * @param mixed $default значение по умолчанию
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }
    /**
     * Получает значение по умолчанию у поля
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }
}