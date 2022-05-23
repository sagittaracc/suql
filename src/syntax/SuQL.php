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
use ReflectionProperty;
use sagittaracc\ArrayHelper;
use suql\annotation\RelationAnnotation;
use suql\core\Scheme;
use suql\core\SmartDate;
use suql\manager\TableEntityManager;
use suql\syntax\field\Field;
use suql\syntax\field\Raw;

/**
 * SuQL синтаксис
 *
 * @author sagittaracc <sagittaracc@gmail.com>
 */
abstract class SuQL extends Obj
{
    /**
     * @var string класс реализующий схему
     */
    protected static $schemeClass = Scheme::class;
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
     * @var string текущая модель разобранная по цепочке аннотаций
     */
    protected $currentAnnotatedModel = null;
    /**
     * @var string|array группировка или индексация данных
     */
    protected $index = null;
    /**
     * @var array перечень функций пост обработки данных
     */
    protected $postFunctions = [];
    /**
     * @var array прогружаемый в модель массив
     */
    protected $data = [];
    /**
     * @var string последняя запрошенная модель
     */
    protected $lastRequestedModel = null;
    /**
     * @var \suql\core\Join последний выполненный join
     */
    protected $lastJoin = null;
    /**
     * @var int идентификатор последней добавленной записи
     */
    protected $lastInsertId = null;
    /**
     * @var boolean сериализовать результат?
     */
    protected $serializeResult = true;
    /**
     * Запрос должен иметь имя запроса
     * @return string
     */
    public function query()
    {
        return $this->queryName ? $this->queryName : str_replace('\\', '_', static::class);
    }
    /**
     * По умолчанию нас интересуют все открытые поля модели
     * @return array
     */
    public function fields()
    {
        $fields = [];
        foreach ($this->getPublicProperties() as $property) {
            $fields[] = $property->getName();
        }
        return $fields;
    }
    /**
     * Инициализация
     * Задание схемы
     * Задание билдера
     */
    public function init()
    {
        $this->setScheme(static::$schemeClass);
        

        if (static::$builderClass) {
            $this->setBuilder(static::$builderClass);
        }
        
        $db = $this->getDb();
        if ($db) {
            $this->setBuilder($db->getBuilder());
        }
    }
    /**
     * Устанавливает текущую таблицу
     * @param string $currentTable
     */
    public function setCurrentTable($currentTable)
    {
        $this->currentTable = $currentTable;
    }
    /**
     * Получает тестовый экземпляр модели
     * @return self
     */
    public static function getTempInstance()
    {
        return new static();
    }
    /**
     * @inheritdoc
     */
    public function setBuilder($builderClass)
    {
        parent::setBuilder($builderClass);
        return $this;
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
        $entityManager = new TableEntityManager();
        $entityManager->persist($this);
        $entityManager->run();
    }
    /**
     * Выборка с начально заданной фильтрацией
     * @return self
     */
    public static function find()
    {
        $instance = static::all();
        call_user_func_array([$instance, 'where'], func_get_args());

        return $instance;
    }
    /**
     * Выборка по первичному ключу
     * @param mixed $id
     * @return self
     */
    public static function findByPK($id)
    {
        $instance = static::all();

        $pk = $instance->getPrimaryKey();
        if (!$pk) {
            return null;
        }

        return $instance->find([$pk => $id]);
    }
    /**
     * Выборка данных по первичному ключу
     * @param mixed $id
     * @return mixed
     */
    public static function one($id)
    {
        return static::findByPK($id)->fetchOne();
    }
    /**
     * Выборка всех данных из модели
     * @return self
     */
    public static function all()
    {
        return new static();
    }
    /**
     * Метод получения данных
     * @return mixed
     */
    abstract public function fetch($method);
    /**
     * Distinct
     * @return self
     */
    public function distinct($options = [])
    {
        $this->getSelect($this->query())->addModifier('distinct');

        if (!empty($options)) {
            $this->select($options);
        }

        return $this;
    }
    /**
     * Выборка определенных полей модели
     * @return self
     */
    public function select($options)
    {
        if (is_string($options)) {
            $options = [$options];
        }

        foreach ($options as $field => $option) {
            if ($option instanceof Raw) {
                $expression = $option;
                $this->getSelect($this->query())->addRaw(
                    str_replace('@', "{$this->currentTable}.", $expression->getExpression())
                );
            }
            else if ($option instanceof Field) {
                $field = $option;
                $this->getSelect($this->query())->addField($this->currentTable, $field->getField());
                foreach ($field->getModifiers() as $modifier => $options) {
                    if (is_string($modifier)) {
                        $params = $options;
                        $this->getSelect($this->query())->getField($this->currentTable, $field->getField())->addModifier($modifier, $params);
                    }
                    else if (is_string($options)) {
                        $modifier = $options;
                        $this->getSelect($this->query())->getField($this->currentTable, $field->getField())->addModifier($modifier);
                    }
                    else if ($options instanceof Closure) {
                        $callbackModifier = $options;
                        $this->getSelect($this->query())->getField($this->currentTable, $field->getField())->addCallbackModifier($callbackModifier);
                    }
                }
            }
            else {
                if (is_integer($field)) {
                    $field = $option;
                    $this->getSelect($this->query())->addField($this->currentTable, $field);
                }
                else {
                    $alias = $option;
                    $this->getSelect($this->query())->addField($this->currentTable, [$field => $alias]);
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
        $this->getSelect($this->query())->addOffset($offset);

        return $this;
    }
    /**
     * LIMIT
     * @param int $limit
     * @return self
     */
    public function limit($limit)
    {
        $this->getSelect($this->query())->addLimit($limit);

        return $this;
    }
    /**
     * Разбор связей указанных в relations
     * @param string $table
     * @param array $relations
     */
    protected function setRelations($table, $relations)
    {
        $firstTable = $table;
        foreach ($relations as $secondClassModel => $on) {
            if (!is_string($secondClassModel)) continue;

            foreach ($on as $secondField => $firstField) break;

            $secondModel = new $secondClassModel();
            $secondTable = $secondModel->table();

            $on = $this->getBuilder()->buildJoinOn($firstTable, $firstField, $secondTable, $secondField);
            $this->getScheme()->rel($firstTable, $secondTable, $on);
        }
    }
    /**
     * Сцепление таблиц
     * @return self
     */
    public function join($option, $type = 'inner', $algorithm = 'simple', $on = '')
    {
        if (is_string($option)) {
            if (class_exists($option) && is_subclass_of($option, SuQL::class)) {
                $model = $option::all();
                $this->setRelations($model->table(), $model->relations());
                $this->join($model->table(), $type, $algorithm);
            }
            else {
                $table = $option;

                if ($this->currentAnnotatedModel) {
                    $annotation = RelationAnnotation::from($this->currentAnnotatedModel)->for($table)->read();
                    if ($annotation->relation) {
                        $on = $this->getBuilder()->buildJoinOn($this->currentTable, $annotation->first_field, $annotation->second_table, $annotation->second_field);
                        $this->getScheme()->rel($this->currentTable, $table, $on);
                        $this->currentAnnotatedModel = $annotation->second_model;
                    }
                }
    
                if ($algorithm === 'simple') {
                    $this->lastJoin = $this->getSelect($this->query())->addJoin($type, $table);
                }
                else if ($algorithm === 'smart') {
                    $this->getSelect($this->query())->addSmartJoin($this->currentTable, $table, $type);
                }
    
                $this->currentTable = $table;
            }
        }
        else if (is_array($option)) {
            foreach ($option as $table => $alias) break;

            $this->lastJoin = $this->getSelect($this->query())->addJoin($type, "$table@$alias");
            $this->getSelect($this->query())->getLastJoin()->setOn($on);

            $this->currentTable = $alias;
        }
        else if ($option instanceof SuQL) {
            $subquery = $option;

            if ($algorithm === 'simple') {
                $this->lastJoin = $this->getSelect($this->query())->addJoin($type, $subquery->query());
            }
            else if ($algorithm === 'smart') {
                $this->getSelect($this->query())->addSmartJoin($this->currentTable, $subquery->query(), $type);
            }

            $this->extend($subquery->getQueries());
            $this->currentTable = $subquery->query();
        }

        return $this;
    }
    /**
     * Пробный вариант
     * @return self
     */
    public function on($leftTableField, $rightTableField)
    {
        if ($this->lastJoin) {
            list($leftTable, $leftField) = explode('.', $leftTableField);
            list($rightTable, $rightField) = explode('.', $rightTableField);

            $this->lastJoin->setOn($this->getBuilder()->buildJoinOn($leftTable, $leftField, $rightTable, $rightField));
        }

        return $this;
    }
    /**
     * Алиас для join
     * @return self
     */
    public function get()
    {
        call_user_func_array([$this, 'join'], func_get_args());
        return $this;
    }
    /**
     * Еще один алиас для join
     * @return self
     */
    public function with()
    {
        call_user_func_array([$this, 'join'], func_get_args());
        return $this;
    }
    /**
     * INNER JOIN
     * @return self
     */
    public function bound($option, $algorithm = 'simple')
    {
        return $this->join($option, 'inner', $algorithm);
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

        $this->getSelect($this->query())->addWhere($where);

        return $this;
    }
    /**
     * Where фильтрация 2.0
     * @return self
     */
    public function whereExpression20($field, $where, $subqueries = [])
    {
        foreach ($subqueries as $subquery) {
            $this->extend($subquery->getQueries());
            $query = $subquery->query();
            $where = str_replace('?', "@$query", $where);
        }

        $this->getSelect($this->query())->addWhere20($field, $where);

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
                    if ($value instanceof SmartDate) {
                        $this->whereExpression20(new FieldName($this->currentTable, $field), $value);
                    }
                    else {
                        $this->whereExpression(
                            new Condition(new SimpleParam(new FieldName($this->currentTable, $field), [$value]), "$ = ?")
                        );
                    }
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
        call_user_func_array([$this, 'where'], func_get_args());
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

            $this->getSelect($this->query())->addField($this->currentTable, $field, false);
            $this->getSelect($this->query())->getField($this->currentTable, $field)->addModifier($direction);
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
            $this->getSelect($this->query())->addField($this->currentTable, $field, false);
            $this->getSelect($this->query())->getField($this->currentTable, $field)->addModifier('group');
        }

        return $this;
    }
    /**
     * Подсчёт количества
     * @return self
     */
    public function count($field = '*')
    {
        $this->getSelect($this->query())->addField($this->currentTable, $field);
        $this->getSelect($this->query())->getField($this->currentTable, $field)->addModifier('count');

        if ($field === '*') {
            return $this->fetchScalar();
        }

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
     * Вернуть данные в виде массива без сериализации
     * @return self
     */
    public function asArray()
    {
        $this->serializeResult = false;
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
    public function getPublicProperties()
    {
        $reflector = new ReflectionClass($this);
        $properties = $reflector->getProperties(ReflectionProperty::IS_PUBLIC);

        return $properties;
    }
    /**
     * Получает primary key у таблицы
     * @return string
     */
    public function getPrimaryKey()
    {
        $db = $this->getDb();

        if (is_null($db)) {
            return '';
        }

        $pkQuery = $this->getBuilder()->getPrimaryKeyQuery($this->table());
        $result = $db->getPdo()->query($pkQuery->getQuery())->fetchAll();
        return !empty($result)
            ? $pkQuery->getColumn('primary', $result)
            : '';
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
     * Получить скаляр
     * @return int
     */
    public function fetchScalar()
    {
        $result = $this->fetchOne();
        return reset($result);
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
     * Задает идентификатор последней добавленной записи
     * @param int $id
     */
    public function setLastInsertId($id)
    {
        $this->lastInsertId = $id;
    }
    /**
     * Получает идентификатор последней добавленной записи
     * @return int
     */
    public function getLastInsertId()
    {
        return $this->lastInsertId;
    }
    /**
     * Получает последнюю запрошенную модель
     * @return string
     */
    public function getLastRequestedModel()
    {
        return $this->lastRequestedModel;
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

        $this->lastRequestedModel = $model;

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

        $data = $this->fetchAll();

        return $data;
    }
}
