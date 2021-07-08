<?php

namespace suql\core;

use suql\core\SchemeInterface;

/**
 * Описание схем отношений между таблицами и вьюхами в базе данных
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Scheme implements SchemeInterface
{
    /**
     * @var array Перечень таблиц
     */
    private $tables = [];
    /**
     * @var array
     * Постоянные связи между таблицами
     * Хранятся постоянно на время работы с базой данных
     */
    private $rel = [];
    /**
     * @var array
     * Временные связи между таблицами или/и вьюхами
     * Чистятся после каждого выполненного запроса
     */
    private $temp_rel = [];
    /**
     * Добавляет определение таблицы в базе данных
     * Задает соответствие между таблицей в бд и названием в suql системе
     * @param string $name название в бд
     * $param string $alias название в suql системе
     */
    public function addTable($name, $alias = null)
    {
        if (!$alias) {
            $alias = $name;
        }

        $this->tables[$name] = $alias;
    }
    /**
     * Добавляет список таблиц
     * @param array $tableList
     */
    public function addTableList($tableList)
    {
        foreach ($tableList as $name => $alias) {
            if (!is_string($name)) {
                $name = $alias;
            }

            $this->addTable($name, $alias);
        }
    }
    /**
     * Проверяет есть алиас для таблицы
     * @param string $name
     * @return boolean
     */
    public function hasTableAlias($name)
    {
        return isset($this->tables[$name]);
    }
    /**
     * Получает алиас для таблицы
     * @param string $name
     * @return string
     */
    public function getTableAlias($name)
    {
        return '{{' . $this->tables[$name] . '}}';
    }
    /**
     * Получает схему таблиц
     * @return array
     */
    public function getTableList()
    {
        return $this->tables;
    }
    /**
     * Получает список постоянных связей между таблицами
     * @return array список постоянных связей
     */
    public function getRel()
    {
        return $this->rel;
    }
    /**
     * Получает список временных связей между таблицами и/или вьюхами
     * @return array список временных связей
     */
    public function getTempRel()
    {
        return $this->temp_rel;
    }
    /**
     * Очищает временные связи
     */
    public function clear()
    {
        $this->temp_rel = [];
    }
    /**
     * Полностью стирает все связи
     */
    public function drop()
    {
        $this->rel = [];
        $this->temp_rel = [];
    }
    /**
     * Устанавливает связь постоянную или временную между двумя сущностями (таблица или вьха)
     * @param string $leftTable первая таблица
     * @param string $rightTable вторая таблица
     * @param string $on связь типа <leftTable>.<field> = <rightTable>.<field>
     * @param boolean $temporary если true то устанавливает временную связь
     */
    public function rel($leftTable, $rightTable, $on, $temporary = false)
    {
        $leftTable = new TableName($leftTable);
        $rightTable = new TableName($rightTable);

        if ($leftTable->alias) {
            $on = str_replace($leftTable->format("%a."), $leftTable->format("%n."), $on);
        }

        if ($rightTable->alias) {
            $on = str_replace($rightTable->format("%a."), $rightTable->format("%n."), $on);
        }

        $scheme = $temporary ? 'temp_rel' : 'rel';
        $leftTableName = $this->hasTableAlias($leftTable->name) ? $this->getTableAlias($leftTable->name) : $leftTable->name;
        $rightTablename = $this->hasTableAlias($rightTable->name) ? $this->getTableAlias($rightTable->name) : $rightTable->name;

        $this->$scheme[$leftTableName][$rightTablename] = $on;
        $this->$scheme[$rightTablename][$leftTableName] = $on;
    }
    /**
     * Устанавливает временную связь между двумя сущностями (таблица или вьха)
     * @param string $leftTable первая таблица
     * @param string $rightTable вторая таблица
     * @param string $on связь типа <leftTable>.<field> = <rightTable>.<field>
     */
    public function temp_rel($leftTable, $rightTable, $on)
    {
        return $this->rel($leftTable, $rightTable, $on, $temporary = true);
    }
    /**
     * Получает полный перечень всех связей
     * @return array перечень постоянных и временных связей
     */
    public function getRels()
    {
        // TODO: Здесь должна возвращаться схема с уже расшифрованными алиасами
        return array_merge($this->rel, $this->temp_rel);
    }
    /**
     * Проверяет есть ли связь между двумя сущностями (таблица или вьюха)
     * @param string $table1
     * @param string $table2
     * @return boolean true если есть, false если нет
     */
    public function hasRelBetween($table1, $table2)
    {
        return isset($this->rel[$table1][$table2])
            || isset($this->temp_rel[$table1][$table2]);
    }
    /**
     * Возвращает тип связи между двумя сущностями (таблицей или вьюхой)
     * @param string $table1
     * @param string $table2
     * @return string rel or temp_rel (постоянная или временная)
     */
    public function getRelTypeBetween($table1, $table2)
    {
        if (isset($this->rel[$table1][$table2])) {
            return 'rel';
        }
        else if (isset($this->temp_rel[$table1][$table2])) {
            return 'temp_rel';
        }
        else {
            return null;
        }
    }
    /**
     * Возвращает связь между двумя сущностями (таблицей или вьюхой)
     * @param string $table1
     * @param string $table2
     * @return string связь в виде <table1>.<field> = <table2>.<field>
     */
    public function getRelBetween($table1, $table2)
    {
        if ($this->hasRelBetween($table1, $table2)) {
            return $this->{$this->getRelTypeBetween($table1, $table2)}[$table1][$table2];
        }
        else {
            return null;
        }
    }
}
