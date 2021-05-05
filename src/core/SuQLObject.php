<?php

namespace suql\core;

use suql\exception\SqlDriverNotSupportedException;
use suql\modifier\field\SQLCaseModifier;
use suql\modifier\field\SQLFilterModifier;
use suql\modifier\field\SQLFunctionModifier;
use suql\modifier\field\SQLGroupModifier;
use suql\modifier\field\SQLOrderModifier;
use suql\modifier\field\SQLWhereModifier;

/**
 * Основной объект, хранящий всю структуру запроса
 * 
 * @author: sagittaracc <sagittaracc@gmail.com>
 */
class SuQLObject
{
    /**
     * @var array перечень конфигураций запросов индексированных их именами в качестве ключей
     */
    private $queries = [];
    /**
     * @var suql\core\SuQLScheme связи между таблицами и вьюхами
     */
    protected $scheme;
    /**
     * @var suql\builder\SQLDriver экземпляр драйвера базы данных
     */
    protected $driver;
    /**
     * @var array параметры которые биндятся к параметризованному запросу
     */
    protected $params = [];
    /**
     * Constructor
     * @param suql\core\SuQLScheme $scheme экземпляр схемы
     * @param suql\builder\SQLDriver $driver экземляр драйвера
     */
    function __construct($scheme, $driver)
    {
        $this->scheme = $scheme;
        $this->driver = $driver;
    }
    /**
     * Получить схему
     * @return suql\core\SuQLScheme
     */
    public function getScheme()
    {
        return $this->scheme;
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
            SQLFilterModifier::class,
            SQLOrderModifier::class,
            SQLGroupModifier::class,
            SQLFunctionModifier::class,
            SQLCaseModifier::class,
        ];
    }
    /**
     * Очистка текущего запроса после выполнения
     */
    public function clear()
    {
        $this->queries = [];
        $this->scheme->clear();
    }
    /**
     * Полный сброс всех настроек
     */
    public function drop()
    {
        $this->queries = [];
        $this->scheme->drop();
        $this->driver = null;
    }
    /**
     * Возвращает список запрошенных запросов или один основной
     * @param array|string='all' $queryList список названий запросов для конвертации
     * или 'all' чтобы получить основной запрос
     * @return array|string
     */
    public function getSQL($queryList)
    {
        if (!$this->driver->getBuilder())
            throw new SqlDriverNotSupportedException();

        if ($queryList === 'all')
            $queryList = $this->getFullQueryList();

        if (!is_array($queryList)) return null;

        $classBuilder = $this->driver->getBuilder();
        $SQLBuilder = new $classBuilder($this);
        $SQLBuilder->run($queryList);
        $sqlList = $SQLBuilder->getSql($queryList);

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
        $this->queries[$name] = new SuQLSelect($this);
    }
    /**
     * Добавляет call запрос вызова хранимой процедуры
     * @param string $name название нового запроса
     * @param string $procName название процедуры
     */
    public function addProcedure($name, $procName)
    {
        $this->queries[$name] = new SuQLProcedure($this, $procName);
    }
    /**
     * Добавляет функцию хранимой процедуры
     * @param string $name название нового запрос
     * @param string $funcName название функции
     */
    public function addFunction($name, $funcName)
    {
        $this->queries[$name] = new SuQLFunction($this, $funcName);
    }
    /**
     * Добавляет insert запрос по названию
     * @param string $name название нового запроса
     */
    public function addInsert($name)
    {
        $this->queries[$name] = new SuQLInsert($this);
    }
    /**
     * Добавляет union запрос по названию
     * @param string $name название запроса
     * @param string $query запрос объединения например @query1 union @query2
     */
    public function addUnion($name, $query)
    {
        $this->queries[$name] = new SuQLUnion($this, $query);
    }
    /**
     * Добавляет таблицу к union запросу по названию
     * @param string $name название запроса
     * @param string $unionType (union|union all)
     * @param string $table элемент union запроса в виде @table_name
     */
    public function addUnionTable($name, $unionType, $table)
    {
        if (!isset($this->queries[$name]))
            $this->queries[$name] = new SuQLUnion($this, $table);
        else
            $this->queries[$name]->addUnionTable($unionType, $table);
    }
    /**
     * Получить объект запроса по имени
     * @param string $name
     * @return suql\core\SuQLSelect
     */
    public function getQuery($name)
    {
        return $this->queries[$name];
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
     * @return класс модификатора
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
     * Проверяет есть ли запрошенный параметр запроса
     * @param string $param
     * @return boolean
     */
    public function hasParam($param)
    {
        return array_key_exists($param, $this->params);
    }
    /**
     * Проверяет если этот параметр не пустой
     * Пустота параметра определяется отдельно
     * в классе обработчика параметра
     * @param string $param
     * @return boolean
     */
    public function hasValuableParam($param)
    {
        return $this->hasParam($param)
            && !is_null($this->params[$param])
            && $this->params[$param]->isValuable();
    }
    /**
     * Получает параметр по имени
     * @param string $param
     * @return suql\core\SuQLParam
     */
    public function getParam($param)
    {
        return $this->params[$param];
    }
    /**
     * Устанавливает параметр запроса
     * @param string $param название параметра
     * @param suql\core\SuQLParam $suqlParam класс параметра
     */
    public function setParam($param, $suqlParam)
    {
        $this->params[$param] = $suqlParam;
    }
    /**
     * Получает список автосгенерированных параметров по плейсхолдерам
     * @return array
     */
    public function getParamList()
    {
        $paramList = [];

        foreach ($this->params as $param)
        {
            foreach ($param->getParamList() as $placeholder => $value)
            {
                if ($value instanceof SuQLPlaceholder) continue;
                $paramList[$placeholder] = $value;
            }
        }

        return $paramList;
    }
}
