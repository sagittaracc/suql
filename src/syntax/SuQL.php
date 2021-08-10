<?php

namespace suql\syntax;

use Closure;
use Exception;
use PDO;
use ReflectionClass;
use suql\core\Condition;
use suql\core\FieldName;
use suql\core\Obj;
use suql\core\SimpleParam;
use suql\syntax\exception\SchemeNotDefined;
use suql\syntax\exception\BuilderNotDefined;
use \ReflectionMethod;
use ReflectionProperty;
use sagittaracc\ArrayHelper;
use suql\syntax\field\Field;
use suql\syntax\field\Raw;

/**
 * SuQL синтаксис
 *
 * @author sagittaracc <sagittaracc@gmail.com>
 */
abstract class SuQL extends Obj implements QueryObject, DbObject
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
     * @var string префикс функции пост обработки
     */
    private $postFunctionPrefix = 'command';
    /**
     * Имя запроса
     * @var string
     */
    protected $queryName = null;
    /**
     * @var string текущая таблица в цепочке вызовов
     */
    protected $currentTable = null;
    /**
     * @var string|array группировка или индексация данных
     */
    private $index = null;
    /**
     * @var array перечень функций пост обработки данных
     */
    private $postFunctions = [];
    /**
     * @var array прогружаемый в модель массив
     */
    private $data = [];
    /**
     * Модель должна содержать перечень полей
     */
    abstract public function fields();
    /**
     * Получает тестовый экземпляр модели
     * @return self
     */
    public static function getTempInstance()
    {
        if (!static::$schemeClass)
            throw new SchemeNotDefined;

        if (!static::$builderClass)
            throw new BuilderNotDefined;

        return new static(null, null);
    }
    /**
     * Получает экземпляр модели
     * @return self
     */
    public static function getInstance()
    {
        if (!static::$schemeClass)
            throw new SchemeNotDefined;

        if (!static::$builderClass)
            throw new BuilderNotDefined();

        $scheme = new static::$schemeClass;
        $builder = new static::$builderClass;

        return new static($scheme, $builder);
    }
    /**
     * Алиас для getInstance
     * @return self
     */
    public static function new()
    {
        return static::getInstance();
    }
    /**
     * Загрузить массив в модель
     * @param array $data
     */
    public static function load($data)
    {
        $instance = static::all();
        $instance->data = $data;
        return $instance;
    }
    /**
     * Сохранить модель
     */
    public function save()
    {
        $this->addInsert($this->query());
        $this->getQuery($this->query())->addInto($this->table());
        foreach ($this->getPublicProperties() as $property) {
            $this->getQuery($this->query())->addValue($property->getName(), $this->{$property->getName()});
        }
        $this->exec($this->getRawSql());
    }
    /**
     * Выборка с начально заданной фильтрацией
     * @return self
     */
    public static function find()
    {
        $instance = static::all();
        call_user_func_array(array($instance, "where"), func_get_args());

        return $instance;
    }
    /**
     * Выборка всех данных из модели
     * @return self
     */
    public static function all()
    {
        $instance = static::getInstance();

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

        $instance->view();
        $instance->select($instance->fields());

        return $instance;
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
            }
            else if ($algorithm === 'smart') {
                $this->getQuery($this->query())->addSmartJoin($this->currentTable, $table, $type);
            }

            $this->currentTable = $table;
        }
        else if ($option instanceof SuQL) {
            $subquery = $option;

            if ($algorithm === 'simple') {
                $this->getQuery($this->query())->addJoin($type, $subquery->query());
            }
            else if ($algorithm === 'smart') {
                $this->getQuery($this->query())->addSmartJoin($this->currentTable, $subquery->query(), $type);
            }

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
     * Union
     * @return self
     */
    public function union($queries)
    {
        $queryList = [];

        foreach ($queries as $query) {
            $this->extend($query->getQueries());
            $queryList[] = '@' . $query->query();
        }

        $this->addUnion($this->query(), implode(' union ', $queryList));

        return $this;
    }
    /**
     * Экспериментальный вариант union
     * @return self
     */
    public function and($queries)
    {
        $queryList = [];

        $queryList[] = '@' . $this->query();
        foreach ($queries as $query) {
            $this->extend($query->getQueries());
            $queryList[] = '@' . $query->query();
        }
        
        $unionQuery = implode(' union ', $queryList);
        $unionQueryName = implode('_', $queryList);

        $this->queryName = $unionQueryName;
        $this->addUnion($unionQueryName, $unionQuery);

        return $this;
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
     * Подсчёт количества
     * @return self
     */
    public function count($field)
    {
        $this->getQuery($this->query())->addField($this->currentTable, $field);
        $this->getQuery($this->query())->getField($this->currentTable, $field)->addModifier('count');

        return $this;
    }
    /**
     * Задать имя запросу
     * @param string $name
     */
    public function as($name)
    {
        $this->renameQuery($this->query(), $name);
        $this->queryName = $name;

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
     * Задает индексацию выбранных данных
     * @param string|array $index
     */
    public function index($index)
    {
        $this->index = $index;

        return $this;
    }
    /**
     * Проверяет прогружены ли в модель данные
     * @return boolean
     */
    public function dataInitiative()
    {
        return !empty($this->data);
    }
    /**
     * Получает public properties модели
     * @return array
     */
    private function getPublicProperties()
    {
        $reflector = new ReflectionClass($this);
        $properties = $reflector->getProperties(ReflectionProperty::IS_PUBLIC);

        return $properties;
    }
    /**
     * Выполнение запроса
     */
    private function exec($query)
    {
        $this->getDb()->exec($query);
    }
    /**
     * Метод получения данных
     * @return mixed
     */
    private function fetch($method)
    {
        $pdoTypes = [
            'integer' => PDO::PARAM_INT,
            'boolean' => PDO::PARAM_BOOL,
            'NULL'    => PDO::PARAM_NULL,
            'double'  => PDO::PARAM_STR,
            'string'  => PDO::PARAM_STR,
        ];

        $methodList = [
            'all' => 'fetchAll',
            'one' => 'fetch',
        ];

        if ($this->dataInitiative()) {
            $this->getDb()->query($this->getBuilder()->createTemporaryTable($this));
            $this->getDb()->query($this->getBuilder()->insertIntoTable($this->table(), $this->data));
        }

        $sth = $this->getDb()->prepare($this->getRawSql());

        foreach ($this->getParamList() as $param => $value) {
            if (isset($pdoTypes[gettype($value)])) {
                $sth->bindValue($param, $value, $pdoTypes[gettype($value)]);
            }
        }

        $sth->execute();

        $data = $sth->{$methodList[$method]}(PDO::FETCH_ASSOC);

        if ($this->index) {
            $data = ArrayHelper::group($this->index, $data);
        }

        if (!empty($this->postFunctions)) {
            foreach ($this->postFunctions as $function) {
                $data = $this->$function($data);
            }
        }

        $result = [];

        // TODO: Вынести куда-нибудь
        $publicProperties = $this->getPublicProperties();
        if (count($publicProperties) > 0) {
            foreach ($data as $row) {
                $instance = static::getTempInstance();
                foreach ($publicProperties as $property) {
                    $instance->{$property->getName()} = $row[$property->getName()];
                }
                $result[] = $instance;
            }
        }
        else {
            $result = $data;
        }

        return $result;
    }
    /**
     * Получение всех данных запроса
     * @return mixed
     */
    public function fetchAll()
    {
        return $this->fetch('all');
    }
    /**
     * Получение одной строки запроса
     * @return mixed
     */
    public function fetchOne()
    {
        return $this->fetch('one');
    }
    /**
     * TODO: Возможно лучше избавиться от этого метода
     * Оставить только $queryName
     * Query
     * @return string
     */
    public function query()
    {
        return $this->queryName ? $this->queryName : str_replace('\\', '_', static::class);
    }
    /**
     * Создавать ли вьюху реальной в базе данных
     * @return boolean
     */
    public function real()
    {
        return false;
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
     * Проверяет если это вьюха
     * @return boolean
     */
    private function isView()
    {
        return $this->viewHasBeenOverriden();
    }
    /**
     * Проверяет если вьюха была переобъявлена
     * @return boolean
     */
    private function viewHasBeenOverriden()
    {
        $reflector = new ReflectionMethod($this, 'view');
        return $reflector->getDeclaringClass()->getName() === get_class($this);
    }
    /**
     * Название функции пост обработчика
     * @param string $name
     * @return string
     */
    private function getPostFunctionName($name)
    {
        return $this->postFunctionPrefix . ucfirst($name);
    }
    /**
     * Проверяет существует ли функция пост обработки
     * @param string $name
     * @return boolean
     */
    private function isPostFunction($name)
    {
        return method_exists($this, $this->getPostFunctionName($name));
    }
    /**
     * Добавление пост обработчика
     * @param string $name название пост обработчика
     */
    private function addPostFunction($name)
    {
        $this->postFunctions[] = $this->getPostFunctionName($name);
    }
    /**
     * Обработка ORM алиасов
     * @return self
     */
    public function __call($name, $arguments)
    {
        if ($this->isPostFunction($name)) {
            // TODO: Возможно нужно будет передать аргументы
            $this->addPostFunction($name);
            return $this;
        }

        $modelNamespace = (new \ReflectionClass(get_class($this)))->getNamespaceName();
        $model = $modelNamespace . '\\' . str_replace('get', '', $name);

        if (!class_exists($model)) {
            throw new Exception("Class $model not defined!");
        }

        $type = isset($arguments[0]) && isset($arguments[0]['join']) ? $arguments[0]['join'] : 'inner';
        $algorithm = isset($arguments[0]) && isset($arguments[0]['algorithm']) ? $arguments[0]['algorithm'] : 'simple';

        $tempInstance = $model::getTempInstance();

        if ($tempInstance->isView()) {
            $this->join($model::all(), $type, $algorithm);
        }
        else {
            $table = $tempInstance->table();
            $fields = $tempInstance->fields();

            $this->join($table, $type, $algorithm);

            foreach ($fields as $field) {
                $this->select([
                    $field,
                ]);
            }
        }

        unset($tempInstance);

        return $this;
    }
}
