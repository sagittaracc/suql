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
use suql\core\Scheme;
use suql\core\SmartDate;
use suql\manager\EntityManager;
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
     * @var string последняя запрошенная модель
     */
    private $lastRequestedModel = null;
    /**
     * @var suql\core\Join последний выполненный join
     */
    private $lastJoin = null;
    /**
     * @var int идентификатор последней добавленной записи
     */
    protected $lastInsertId = null;
    /**
     * @var boolean сериализовать результат?
     */
    private $serializeResult = true;
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
        $entityManager = new EntityManager();
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
        $instance = new static();

        $instance->lastRequestedModel = static::class;
        $instance->currentAnnotatedModel = static::class;
        $instance->currentTable = null;

        $instance->setScheme(static::$schemeClass);
        $instance->addSelect($instance->query());

        if ($instance instanceof TableInterface) {
            $instance->init();

            $option = $instance->table();
            if (is_string($option)) {
                $table = $option;
                $instance->getQuery($instance->query())->addFrom($table);
                $instance->currentTable = $table;
            }
            else if (is_array($option)) {
                foreach ($option as $table => $alias) break;
                $instance->getQuery($instance->query())->addFrom("$table@$alias");
                $instance->currentTable = $alias;
            }
            else if ($option instanceof SuQL) {
                $subquery = $option;
                $instance->getQuery($instance->query())->addFrom($subquery->query());
                $instance->extend($subquery->getQueries());
                $instance->currentTable = $subquery->query();
            }

            $view = $instance->view();
            if (is_string($view)) {
                $viewQuery = $instance->getBuilder()->createView($instance);
                $instance->getDb()->getPdo()->exec($viewQuery);
            }

            $instance->select($instance->fields());

            $instance->setRelations($instance->table(), $instance->relations());
        }

        return $instance;
    }
    /**
     * Distinct
     * @return self
     */
    public function distinct($options = [])
    {
        $this->getQuery($this->query())->addModifier('distinct');

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
     * Разбор связей указанных в relations
     * @param string $table
     * @param array $relations
     */
    private function setRelations($table, $relations)
    {
        $firstTable = $table;
        foreach ($relations as $secondClassModel => $on) {
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
                    $annotation = Annotation::from($this->currentAnnotatedModel)->for($table)->read();
                    if ($annotation->relation) {
                        $on = $this->getBuilder()->buildJoinOn($this->currentTable, $annotation->first_field, $annotation->second_table, $annotation->second_field);
                        $this->getScheme()->rel($this->currentTable, $table, $on);
                        $this->currentAnnotatedModel = $annotation->second_model;
                    }
                }
    
                if ($algorithm === 'simple') {
                    $this->lastJoin = $this->getQuery($this->query())->addJoin($type, $table);
                }
                else if ($algorithm === 'smart') {
                    $this->getQuery($this->query())->addSmartJoin($this->currentTable, $table, $type);
                }
    
                $this->currentTable = $table;
            }
        }
        else if (is_array($option)) {
            foreach ($option as $table => $alias) break;

            $this->lastJoin = $this->getQuery($this->query())->addJoin($type, "$table@$alias");
            $this->getQuery($this->query())->getLastJoin()->setOn($on);

            $this->currentTable = $alias;
        }
        else if ($option instanceof SuQL) {
            $subquery = $option;

            if ($algorithm === 'simple') {
                $this->lastJoin = $this->getQuery($this->query())->addJoin($type, $subquery->query());
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

        $this->getQuery($this->query())->addWhere20($field, $where);

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
    public function count($field = '*')
    {
        $this->getQuery($this->query())->addField($this->currentTable, $field);
        $this->getQuery($this->query())->getField($this->currentTable, $field)->addModifier('count');

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

        $db = $this->getDb();

        if ($this->dataInitiative()) {
            $db->getPdo()->query($this->getBuilder()->createTemporaryTable($this));
            $db->getPdo()->query($this->getBuilder()->insertIntoTable($this->table(), $this->data));
        }
        else {
            $config = $db->getConfig();
            $table = $this->table();

            $tableExistsQuery = $db->getPdo()->query($this->getBuilder()->tableExistsQuery($config, $table));
            $tableExists = $tableExistsQuery && $table ? $tableExistsQuery->fetchColumn() : true;
            if (!$tableExists) {
                $this->create();
                $db->getPdo()->query($this->getBuilder()->buildModel($this));
            }
        }

        $sth = $db->getPdo()->prepare($this->getRawSql());

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

        $result = [];

        // TODO: Сериализацию необходимо проверить
        if ($this->lastRequestedModel) {
            $lastRequestedModelName = $this->lastRequestedModel;
            $lastRequestedModel = $lastRequestedModelName::getTempInstance();
            $publicProperties = $lastRequestedModel->getPublicProperties();
            if ($this->serializeResult && count($publicProperties) > 0) {
                if ($method === 'all') {
                    foreach ($data as $row) {
                        $instance = $lastRequestedModel::all();
                        foreach ($publicProperties as $property) {
                            $instance->{$property->getName()} = $row[$property->getName()];
                        }
                        $pk = $instance->getPrimaryKey();
                        if ($pk) {
                            $instance->where([$pk => $instance->$pk]);
                        }
                        $result[] = $instance;
                    }
                }
                else if ($method === 'one') {
                    $instance = $lastRequestedModel::all();
                    if ($data) {
                        foreach ($publicProperties as $property) {
                            $instance->{$property->getName()} = $data[$property->getName()];
                        }
                    }
                    $result = $instance;
                }
            }
            else {
                $result = $data;
            }
        }
        else {
            $result = $data;
        }

        if (!empty($this->postFunctions)) {
            foreach ($this->postFunctions as $function) {
                $result = $this->$function($result);
            }
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
