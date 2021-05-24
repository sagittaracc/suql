<?php

namespace suql\core;

use sagittaracc\PlaceholderHelper;
use suql\core\InsertQueryInterface;

/**
 * Обработчик insert запросов
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Insert extends Query implements InsertQueryInterface
{
    /**
     * @var string таблица в которую осуществляется добавление записей
     */
    private $table = null;
    /**
     * @var array массив записей для добавления
     */
    private $values = [];
    /**
     * Задать таблицу в которую будут добавляться записи
     * @param string $table название таблицы
     */
    public function addInto($table)
    {
        $this->table = $table;
    }
    /**
     * Добавить значение поля для добавления
     * @param string $field название поля
     * @param string $value значение поля
     */
    public function addValue($field, $value)
    {
        $this->values[$field] = (new PlaceholderHelper("?"))->bind($value);
    }
    /**
     * Задать добавление значения по плейсхолдеру
     * @param string $field название поля
     * @param string $placeholder название плейсхолдера
     */
    public function addPlaceholder($field, $placeholder)
    {
        $this->values[$field] = $placeholder;
    }
    /**
     * Получить таблицу в которую добавляются записи
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }
    /**
     * Получить строку с перечнем полей для добавления
     * @return string
     */
    public function getFields()
    {
        return implode(',', array_keys($this->values));
    }
    /**
     * Получить строку с перечнем значений для добавления
     * @return string
     */
    public function getValues()
    {
        return implode(',', array_values($this->values));
    }
}
