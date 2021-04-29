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
     * Constructor
     * @param suql\core\SuQLFieldName $field объект поля
     * @param string $condition условное выражение
     */
    function __construct($field, $condition)
    {
        $this->field = $field;
        $this->condition = $condition;
    }
    /**
     * Конвертирует в строку
     */
    public function __toString()
    {
        return str_replace('$', $this->field->format('%t.%n'), $this->condition);
    }
}