<?php

namespace suql\core;

/**
 * Простое условное выражение типа <field> <condition> <value>
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class SuQLCondition
{
    /**
     * @var suql\core\SuQLFieldName имя поля
     */
    private $field;
    /**
     * @var string условное выражение
     */
    private $condition;
    /**
     * @var string формат вывода поля
     */
    private $format;
    /**
     * Constructor
     * @param suql\core\SuQLFieldName $field объект поля
     * @param string $condition условное выражение
     * @param string $format формат вывода поля
     */
    function __construct($field, $condition, $format = '%n')
    {
        $this->field = $field;
        $this->condition = $condition;
        $this->format = $format;
    }
    /**
     * Установить формат вывода поля
     * @param string $format
     */
    public function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }
    /**
     * Конвертирует в строку
     * @return string
     */
    public function __toString()
    {
        return str_replace('$', $this->field->format($this->format), $this->condition);
    }
}