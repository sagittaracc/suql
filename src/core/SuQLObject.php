<?php

namespace core;

use suql\exception\SqlDriverNotSupportedException;

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
     * @var core\SuQLScheme связи между таблицами и вьюхами
     */
    protected $scheme;
    /**
     * @var suql\builder\SQLDriver экземпляр драйвера базы данных
     */
    protected $driver;
    /**
     * @var array параметры которые биндятся к параметризованному запросу
     */
    public $params = [];
    /**
     * Constructor
     * @param core\SuQLScheme $scheme экземпляр схемы
     */
    function __construct($scheme, $driver)
    {
        $this->scheme = $scheme;
        $this->driver = $driver;
    }
    /**
     * Получить схему
     * @return core\SuQLScheme
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
            'SQLBaseModifier',
            'SQLWhereModifier',
            'SQLFilterModifier',
            'SQLOrderModifier',
            'SQLGroupModifier',
            'SQLFunctionModifier',
            'SQLCaseModifier',
            'SQLConditionModifier',
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
     * @return core\SuQLSelect
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
     * @return core\SuQLParam
     */
    public function getParam($param)
    {
        return $this->params[$param];
    }
    /**
     * Устанавливает параметр запроса
     * @param string $param название параметра
     * @param core\SuQLParam $suqlParam класс параметра
     */
    public function setParam($param, $suqlParam)
    {
        $this->params[$param] = $suqlParam;
    }
}
