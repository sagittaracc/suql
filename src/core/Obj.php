<?php

namespace suql\core;

use Exception;
use suql\exception\WrongSchemaException;
use suql\modifier\field\SQLAsModifier;
use suql\modifier\field\SQLCaseModifier;
use suql\modifier\field\SQLFunctionModifier;
use suql\modifier\field\SQLGroupModifier;
use suql\modifier\field\SQLOrderModifier;
use suql\modifier\field\SQLWhereModifier;

/**
 * Основной объект, хранящий всю структуру запроса
 * 
 * @author: sagittaracc <sagittaracc@gmail.com>
 */
class Obj
{
    /**
     * @var array перечень конфигураций запросов индексированных их именами в качестве ключей
     */
    private $queries = [];
    /**
     * @var \suql\core\Scheme связи между таблицами и вьюхами
     */
    protected $scheme;
    /**
     * @var \suql\builder\SQLBuilder экземпляр билдера
     */
    protected $builder;
    /**
     * @var array параметры которые биндятся к параметризованному запросу
     */
    protected $params = [];
    /**
     * Constructor
     */
    function __construct()
    {
    }
    /**
     * Установить схему
     * @param string $schemeClass класс описывающий схему
     */
    public function setScheme(string $schemeClass): void
    {
        if (class_exists($schemeClass)) {
            $this->scheme = new $schemeClass;
        }
        else {
            throw new WrongSchemaException($schemeClass);
        }
    }
    /**
     * Получить схему
     * @return \suql\core\Scheme
     */
    public function getScheme(): Scheme
    {
        return $this->scheme;
    }
    /**
     * Задает билдер
     * @param string класс билдера
     */
    public function setBuilder($builderClass)
    {
        if (class_exists($builderClass)) {
            $this->builder = new $builderClass;
        }
    }
    /**
     * Получить builder
     * @return \suql\builder\SQLBuilder
     */
    public function getBuilder()
    {
        return $this->builder;
    }
    /**
     * Перечень используемых модификаторов
     * TODO: Возможно в будущем отказаться от использования так как
     * класс используемого модификатора будет указываться при его
     * использовании при формировании запроса.
     * Сейчас указывается только имя модификатора и класс в котором
     * он определен определяется автоматически, что может вызывать
     * конфликт имен разных модификаторов с одинаковым именем.
     * @return array массив имен классов используемых модификаторов
     */
    protected function modifierList()
    {
        return [
            SQLWhereModifier::class,
            SQLOrderModifier::class,
            SQLGroupModifier::class,
            SQLFunctionModifier::class,
            SQLCaseModifier::class,
            SQLAsModifier::class,
        ];
    }
    /**
     * Очистка текущего запроса после выполнения
     */
    public function clear()
    {
        $this->queries = [];
        $this->builder->clear();
        $this->scheme->clear();
    }
    /**
     * Полный сброс всех настроек
     */
    public function drop()
    {
        $this->clear();
        $this->scheme->drop();
    }
    /**
     * Возвращает список запрошенных запросов или один основной
     * @param array|string='all' $queryList список названий запросов для конвертации
     * или 'all' чтобы получить основной запрос
     * @return array|string
     */
    public function getSQL($queryList)
    {
        if ($queryList === 'all')
            $queryList = $this->getFullQueryList();

        if (!is_array($queryList)) return null;

        $this->builder->assign($this);
        $this->builder->run($queryList);
        $sqlList = $this->builder->getSql($queryList);

        $this->clear();

        return $sqlList;
    }
    /**
     * Объект с параметрами всех запросов
     * @return array
     */
    public function getQueries()
    {
        return $this->queries;
    }
    /**
     * Расширить текущий объект конфигурации запросов
     * @param array $queries
     */
    public function extend($queries)
    {
        $this->queries = array_merge($this->queries, $queries);
    }
    /**
     * Список названий всех запросов
     * @return array
     */
    public function getFullQueryList()
    {
        return array_keys($this->queries);
    }
    /**
     * Добавляет select запрос по названию
     * @param string $name название нового запроса
     */
    public function addSelect($name)
    {
        $this->queries[$name] = new Select($this);
    }
    /**
     * Добавляет call запрос вызова хранимой процедуры
     * @param string $name название нового запроса
     * @param string $procName название процедуры
     */
    public function addProcedure($name, $procName)
    {
        $this->queries[$name] = new Proc($this, $procName);
    }
    /**
     * Добавляет функцию хранимой процедуры
     * @param string $name название нового запрос
     * @param string $funcName название функции
     */
    public function addFunction($name, $funcName)
    {
        $this->queries[$name] = new Func($this, $funcName);
    }
    /**
     * Добавляет insert запрос по названию
     * @param string $name название нового запроса
     */
    public function addInsert($name)
    {
        $this->queries[$name] = new Insert($this);
    }
    /**
     * Добавляет update запрос по названию
     * @param string $name название нового запроса
     */
    public function addUpdate($name)
    {
        $this->queries[$name] = new Update($this);
    }
    /**
     * Добавляет union запрос по названию
     * @param string $name название запроса
     * @param string $query запрос объединения например @query1 union @query2
     */
    public function addUnion($name, $query)
    {
        $this->queries[$name] = new Union($this, $query);
    }
    /**
     * Добавляет таблицу к union запросу по названию
     * @param string $name название запроса
     * @param string $unionType (union|union all)
     * @param string $table элемент union запроса в виде @table_name
     */
    public function addUnionTable($name, $unionType, $table)
    {
        if (!isset($this->queries[$name])) {
            $this->queries[$name] = new Union($this, $table);
        }
        else {
            $this->queries[$name]->addUnionTable($unionType, $table);
        }
    }
    /**
     * Получить объект запроса по имени
     * @param string $name
     * @return \suql\core\Query
     */
    public function getQuery($name)
    {
        return $this->queries[$name];
    }
    /**
     * Получить перечень полей в select
     * @param string $name
     * @return \suql\core\Select
     */
    public function getSelect($name)
    {
        $query = $this->getQuery($name);

        if ($query instanceof Select) {
            return $query;
        }
        else {
            // TODO: дописать обработку
        }
    }
    /**
     * Установить объект запроса по имени
     * @param string $name
     * @param \suql\core\Query $query
     */
    public function setQuery($name, $query)
    {
        $this->queries[$name] = $query;
    }
    /**
     * Удалить объект запроса по имени
     * @param string $name
     */
    private function flushQuery($name)
    {
        unset($this->queries[$name]);
    }
    /**
     * Переименовать запрос
     * @param string $old
     * @param string $new
     */
    public function renameQuery($old, $new)
    {
        if (!$this->getQuery($old)) {
            throw new Exception("Query $old doesn't exist!");
        }

        $buf = $this->getQuery($old);
        $this->flushQuery($old);
        $this->setQuery($new, $buf);
    }
    /**
     * Проверяет есть ли запрос по имени
     * @param string $name
     * @return boolean
     */
    public function hasQuery($name)
    {
        return isset($this->queries[$name]);
    }
    /**
     * Ищет класс обработчика модификатора по имени
     * @param string $modifierHandler название модификатора
     * @return \suql\modifier\field\SQLBaseModifier
     */
    public function getModifierClass($modifierHandler)
    {
        foreach ($this->modifierList() as $modifierClass) {
            if (method_exists($modifierClass, $modifierHandler))
                return $modifierClass;
        }

        return null;
    }
    /**
     * Получает параметр по имени
     * @param string $param
     * @return mixed
     */
    public function getParam($param)
    {
        return $this->params[$param];
    }
    /**
     * Устанавливает параметр запроса
     * @param string $param название параметра
     * @param mixed $value значение параметра
     */
    public function setParam($param, $value)
    {
        $this->params[$param] = $value;
    }
    /**
     * Получает список автосгенерированных параметров по плейсхолдерам
     * @return array
     */
    public function getParamList()
    {
        $paramList = [];

        foreach ($this->params as $placeholder => $param)
        {
            if ($param instanceof Placeholder) continue;

            $paramList[$placeholder] = $param;
        }

        return $paramList;
    }
}
