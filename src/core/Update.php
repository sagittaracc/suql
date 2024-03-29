<?php

namespace suql\core;

use sagittaracc\PlaceholderHelper;
use suql\core\param\Simple;
use suql\core\UpdateQueryInterface;

/**
 * Обработчик update запросов
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Update extends Query implements UpdateQueryInterface, Buildable
{
    /**
     * @var string таблица в которой обновляем записи
     */
    private $table = null;
    /**
     * @var array массив записей для обновления
     */
    private $values = [];
    /**
     * 
     */
    private $where;
    /**
     * @inheritdoc
     */
    public function getBuilderFunction()
    {
        return 'buildUpdateQuery';
    }
    /**
     * Задать таблицу в которой обновляем
     * @param string $table название таблицы
     */
    public function setTable($table)
    {
        $this->table = $table;
    }
    /**
     * Добавить значение поля для обновления
     * @param string $field название поля
     * @param string $value значение поля
     */
    public function addValue($field, $value)
    {
        $this->values[$field] = (new PlaceholderHelper("?"))->bind($value);
    }
    /**
     * Задать обновление значения по плейсхолдеру
     * @param string $field название поля
     * @param string $placeholder название плейсхолдера
     */
    public function addPlaceholder($field, $placeholder)
    {
        $this->values[$field] = $placeholder;
    }
    /**
     * Добавляет условие where 2.0 TODO: Переименовать в addWhere (без 2.0)
     * @param \suql\core\FieldName $fieldName
     * @param mixed $condition
     */
    public function addWhere($field, $condition)
    {
        $fieldName = new FieldName($this->table, $field);

        $this->where20[] = [
            'fieldName' => $fieldName,
            'condition' => $condition,
        ];
    }
    /**
     * Получить таблицу
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }
    /**
     * @return string
     */
    public function getValues()
    {
        $updateList = [];
        foreach ($this->values as $field => $value) {
            $updateList[] = "$field = $value";
        }
        return implode(',', $updateList);
    }
    /**
     * 
     */
    public function getWhere()
    {
        return [];
    }
    /**
     * 
     */
    public function getWhere20()
    {
        return $this->where20;
    }
    /**
     * 
     */
    public function getFieldList()
    {
        return [];
    }
}
