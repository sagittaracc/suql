<?php

namespace suql\syntax;

use Closure;
use Exception;
use PDO;
use suql\core\Condition;
use suql\core\FieldName;
use suql\core\Obj;
use suql\core\SimpleParam;
use suql\syntax\exception\SchemeNotDefined;
use suql\syntax\exception\BuilderNotDefined;

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
     * @var string используемый билдер
     */
    protected static $builderClass = null;
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
        
        if (!static::$builderClass)
            throw new BuilderNotDefined();

        $scheme = new static::$schemeClass;
        $builder = new static::$builderClass;

        $instance = new static($scheme, $builder);
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
    public function distinct()
    {
        $this->getQuery($this->query())->addModifier('distinct');

        return $this;
    }
    /**
     * Выборка определенных полей модели
     * @return self
     */
    public function select($options)
    {
        foreach ($options as $field => $option) {
            if ($option instanceof Raw) {
                $expression = $option;
                $this->getQuery($this->query())->addRaw(
                    str_replace('@', "{$this->currentTable}.", $expression->getExpression())
                );
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
    public function join($option, $type = 'inner', $algorithm = 'simple')
    {
        if (is_string($option)) {
            $table = $option;

            if ($algorithm === 'simple') {
                $this->getQuery($this->query())->addJoin($type, $table);
                $this->currentTable = $table;
            }
            else if ($algorithm === 'smart') {
                $this->getQuery($this->query())->addSmartJoin($this->currentTable, $table, $type);
                $this->currentTable = $table;
            }
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
     * Алиас для join
     * @return self
     */
    public function get($option)
    {
        return $this->join($option);
    }
    /**
     * LEFT JOIN
     * @return self
     */
    public function leftJoin($option, $algorithm = 'simple')
    {
        return $this->join($option, 'left', $algorithm);
    }
    /**
     * RIGHT JOIN
     * @return self
     */
    public function rightJoin($option, $algorithm = 'simple')
    {
        return $this->join($option, 'right', $algorithm);
    }
    /**
     * Where фильтрация
     * @return self
     */
    public function whereExpression($where, $subqueries = [])
    {
        foreach ($subqueries as $subquery) {
            $this->extend($subquery->getQueries());
            $query = $subquery->query();
            $where = str_replace('?', "@$query", $where);
        }

        $this->getQuery($this->query())->addWhere($where);

        return $this;
    }
    /**
     * Where фильтрация
     * @return self
     */
    public function where()
    {
        if (func_num_args() === 1) {
            $where = func_get_arg(0);
            if (is_string($where)) {
                $this->whereExpression($where);
            }
            else if (is_array($where)) {
                foreach ($where as $field => $value) {
                    $this->whereExpression(
                        new Condition(new SimpleParam(new FieldName($this->currentTable, $field), [$value]), "$ = ?")
                    );
                }
            }
        }
        else if (func_num_args() === 2) {
            $where = func_get_arg(0);
            $subqueries = func_get_arg(1);
            $this->whereExpression($where, $subqueries);
        }
        else if (func_num_args() === 3) {
            $field = func_get_arg(0);
            $compare = func_get_arg(1);
            $value = func_get_arg(2);
            $this->whereExpression(
                new Condition(new SimpleParam(new FieldName($this->currentTable, $field), [$value]), "$ $compare ?")
            );
        }

        return $this;
    }
    /**
     * Алиас для функции where
     * @return self
     */
    public function andWhere()
    {
        call_user_func_array(array($this, "where"), func_get_args());
        return $this;
    }
    /**
     * Сортировка
     * @return self
     */
    public function order($order)
    {
        foreach ($order as $field => $options) {
            if (is_integer($field)) {
                $field = $options;
                $direction = 'asc';
            }
            else {
                $direction = $options;
            }

            $this->getQuery($this->query())->addField($this->currentTable, $field, false);
            $this->getQuery($this->query())->getField($this->currentTable, $field)->addModifier($direction);
        }

        return $this;
    }
    /**
     * Группировка
     * @return self
     */
    public function group($fields)
    {
        if (is_string($fields)) {
            $fields = [$fields];
        }

        foreach ($fields as $field) {
            $this->getQuery($this->query())->addField($this->currentTable, $field, false);
            $this->getQuery($this->query())->getField($this->currentTable, $field)->addModifier('group');
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
    /**
     * Получение всех данных запроса
     * @return mixed
     */
    public function fetchAll()
    {
        $pdoTypes = [
            'integer' => PDO::PARAM_INT,
            'boolean' => PDO::PARAM_BOOL,
            'NULL'    => PDO::PARAM_NULL,
            'double'  => PDO::PARAM_STR,
            'string'  => PDO::PARAM_STR,
        ];

        $sth = $this->getDb()->prepare($this->getRawSql());

        foreach ($this->getParamList() as $param => $value) {
            if (isset($pdoTypes[gettype($value)])) {
                $sth->bindValue($param, $value, $pdoTypes[gettype($value)]);
            }
        }

        $sth->execute();

        return $sth->fetchAll(PDO::FETCH_ASSOC);
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
    /**
     * Обработка ORM алиасов
     * @return self
     */
    public function __call($name, $arguments)
    {
        $modelNamespace = (new \ReflectionClass(get_class($this)))->getNamespaceName();
        $model = $modelNamespace . '\\' . str_replace('get', '', $name);

        if (!class_exists($model)) {
            throw new Exception("Class $model not defined!");
        }

        $instance = new $model(null, null);
        $table = $instance->table();
        $fields = $instance->fields();

        $this->join($table, 'inner', 'smart');

        foreach ($fields as $field) {
            $this->select([
                $field,
            ]);
        }

        return $this;
    }
}