<?php

namespace suql\syntax;

use suql\core\Obj;
use sagittaracc\ArrayHelper;
use suql\builder\SQLDriver;
use suql\syntax\exception\SchemeNotDefined;
use suql\syntax\exception\SqlDriverNotDefined;

/**
 * SuQL синтаксис
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
abstract class SuQL extends Obj implements QueryObject
{
    /**
     * @var string класс реализующий схему
     */
    protected static $schemeClass = null;
    /**
     * @var string используемый драйвер
     */
    protected static $sqlDriver = null;
    /**
     * @var string текущая таблица в цепочке вызовов
     */
    private $currentTable = null;
    /**
     * Получает экземпляр модели
     * @return self
     */
    private static function getInstance()
    {
        if (!static::$schemeClass)
            throw new SchemeNotDefined;

        if (!static::$sqlDriver)
            throw new SqlDriverNotDefined;

        $scheme = new static::$schemeClass;
        $driver = new SQLDriver(static::$sqlDriver);

        $instance = new static($scheme, $driver);
        $instance->addSelect($instance->query());
        $instance->getQuery($instance->query())->addFrom($instance->table());
        $instance->currentTable = $instance->table();

        return $instance;
    }
    /**
     * Выборка всех данных из модели
     * @return self
     */
    public static function all()
    {
        return static::getInstance()->view();
    }
    /**
     * Выборка определенных полей модели
     * @return self
     */
    public function select($fieldList)
    {
        if (ArrayHelper::isSequential($fieldList)) {
            foreach ($fieldList as $field) {
                $this->getQuery($this->query())->addField($this->currentTable, $field);
            }
        }
        else {
            foreach ($fieldList as $field => $alias) {
                $this->getQuery($this->query())->addField($this->currentTable, [$field => $alias]);
            }
        }

        return $this;
    }
    /**
     * Сцепление таблиц
     * @return self
     */
    public function join($table)
    {
        $this->getQuery($this->query())->addJoin('inner', $table);
        $this->currentTable = $table;

        return $this;
    }
    /**
     * Сортировка
     * @return self
     */
    public function order($order)
    {
        foreach ($order as $field => $direction) {
            $this->getQuery($this->query())->addField($this->currentTable, $field, false);
            $this->getQuery($this->query())->getField($this->currentTable, $field)->addModifier($direction);
        }

        return $this;
    }
    /**
     * Возвращает sql
     * @return string
     */
    public function getRawSql()
    {
        return $this->getSQL([$this->query()]);
    }
}