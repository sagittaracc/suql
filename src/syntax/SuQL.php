<?php

namespace suql\syntax;

use Closure;
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

        $option = $instance->table();
        if (is_string($option)) {
            $table = $option;
            $instance->getQuery($instance->query())->addFrom($table);
            $instance->currentTable = $table;
        }
        else if ($option instanceof SuQL) {
            $subquery = $option;
            $instance->getQuery($instance->query())->addFrom($subquery->query());
            $instance->extend($subquery->getQueries());
            $instance->currentTable = $subquery->query();
        }

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
     * Distinct
     * @return self
     */
    public static function distinct()
    {
        $instance = static::getInstance();
        $instance->getQuery($instance->query())->addModifier('distinct');

        return $instance->view();
    }
    /**
     * Выборка определенных полей модели
     * @return self
     */
    public function select($options)
    {
        if (ArrayHelper::isSequential($options)) {
            foreach ($options as $option) {
                if ($option instanceof Raw) {
                    $expression = $option;
                    $this->getQuery($this->query())->addRaw($expression->getExpression());
                }
                else if ($option instanceof Field) {
                    $field = $option;
                    $this->getQuery($this->query())->addField($this->currentTable, $field->getField());
                    foreach ($field->getModifiers() as $modifier => $options) {
                        if (is_string($modifier)) {
                            $params = $options;
                            $this->getQuery($this->query())->getField($this->currentTable, $field->getField())->addModifier($modifier, $params);
                        }
                        else if (is_string($options)) {
                            $modifier = $options;
                            $this->getQuery($this->query())->getField($this->currentTable, $field->getField())->addModifier($modifier);
                        }
                        else if ($options instanceof Closure) {
                            $callbackModifier = $options;
                            $this->getQuery($this->query())->getField($this->currentTable, $field->getField())->addCallbackModifier($callbackModifier);
                        }
                    }
                }
                else {
                    $field = $option;
                    $this->getQuery($this->query())->addField($this->currentTable, $field);
                }
            }
        }
        else {
            foreach ($options as $field => $option) {
                if ($option instanceof Raw) {
                    $expression = $option;
                    $this->getQuery($this->query())->addRaw($expression->getExpression());
                }
                else if ($option instanceof Field) {
                    $field = $option;
                    $this->getQuery($this->query())->addField($this->currentTable, $field->getField());
                    foreach ($field->getModifiers() as $modifier => $options) {
                        if (is_string($modifier)) {
                            $params = $options;
                            $this->getQuery($this->query())->getField($this->currentTable, $field->getField())->addModifier($modifier, $params);
                        }
                        else if (is_string($options)) {
                            $modifier = $options;
                            $this->getQuery($this->query())->getField($this->currentTable, $field->getField())->addModifier($modifier);
                        }
                        else if ($options instanceof Closure) {
                            $callbackModifier = $options;
                            $this->getQuery($this->query())->getField($this->currentTable, $field->getField())->addCallbackModifier($callbackModifier);
                        }
                    }
                }
                else {
                    if (is_integer($field)) {
                        $field = $option;
                        $this->getQuery($this->query())->addField($this->currentTable, $field);
                    }
                    else {
                        $alias = $option;
                        $this->getQuery($this->query())->addField($this->currentTable, [$field => $alias]);
                    }
                }
            }
        }

        return $this;
    }
    /**
     * OFFSET
     * @param int $offset
     * @return self
     */
    public function offset($offset)
    {
        $this->getQuery($this->query())->addOffset($offset);

        return $this;
    }
    /**
     * LIMIT
     * @param int $limit
     * @return self
     */
    public function limit($limit)
    {
        $this->getQuery($this->query())->addLimit($limit);

        return $this;
    }
    /**
     * Сцепление таблиц
     * @return self
     */
    public function join($option, $type = 'inner')
    {
        if (is_string($option)) {
            $table = $option;

            $this->getQuery($this->query())->addJoin($type, $table);
            $this->currentTable = $table;
        }
        else if ($option instanceof SuQL) {
            $subquery = $option;

            $this->getQuery($this->query())->addJoin($type, $subquery->query());
            $this->extend($subquery->getQueries());
            $this->currentTable = $subquery->query();
        }

        return $this;
    }
    /**
     * LEFT JOIN
     * @return self
     */
    public function leftJoin($option)
    {
        return $this->join($option, 'left');
    }
    /**
     * RIGHT JOIN
     * @return self
     */
    public function rightJoin($option)
    {
        return $this->join($option, 'right');
    }
    /**
     * Where фильтрация
     * @return self
     */
    public function where($where, $subqueries = [])
    {
        foreach ($subqueries as $index => $subquery) {
            $this->extend($subquery->getQueries());
            $query = $subquery->query();
            $where = str_replace('?', "@$query", $where);
        }

        $this->getQuery($this->query())->addWhere($where);

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
     * Группировка
     * @return self
     */
    public function group($field)
    {
        $this->getQuery($this->query())->addField($this->currentTable, $field, false);
        $this->getQuery($this->query())->getField($this->currentTable, $field)->addModifier('group');

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
    /**
     * Query
     * @return string
     */
    public function query()
    {
        return str_replace('\\', '_', static::class);
    }
    /**
     * View
     * @return self
     */
    public function view()
    {
        return $this;
    }
}