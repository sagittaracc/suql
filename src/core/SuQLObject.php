<?php

namespace core;

use builder\SQLDriver;

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
     * @var array связи между таблицами и вьюхами
     * @rel - постоянные связи между таблицами базы данных
     * @temp_rel - временные связи между таблицами/вьюхами и временно созданными вьюхами
     * 
     * TODO: Проверить возможно на данный момент уже не используется @temp_rel
     * Вынести обработку схем в отдельный обработчик схем
     */
    private $scheme  = ['rel' => [], 'temp_rel' => []];
    /**
     * @var string драйвер базы данных который мы собираемся использовать (mysql, postgresql etc.)
     */
    protected $driver = null;
    /**
     * @var array лог ведения ошибок
     * TODO: Вынести работу с логом в отдельный обработчик лога
     * На данный момент практически не используется - ждет реализации
     */
    private $log = [];
    /**
     * @var array параметры которые биндятся к параметризованному запросу
     */
    public $params = [];

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
        $this->scheme['temp_rel'] = [];
    }

    /**
     * Полный сброс всех настроек
     */
    public function drop()
    {
        $this->queries = [];
        $this->scheme['rel'] = [];
        $this->scheme['temp_rel'] = [];
        $this->driver = null;
    }

    /**
     * Установка используемого драйвера СУБД
     * @param string $driver используемый драйвер (mysql, postgresql etc.)
     * @return core\SuQLObject self
     */
    public function setDriver($driver)
    {
        if (SQLDriver::exists($driver))
            $this->driver = $driver;

        return $this;
    }

    /**
     * Получить используемый драйвер СУБД
     * @return string используемый драйвер (mysql, postgresql etc.)
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Записать возникшую ошибку в лог
     */
    protected function setError($error)
    {
        $this->log['error'][] = $error;
    }

    /**
     * Записать предупреждение в лог
     */
    protected function setWarning($warning)
    {
        $this->log['warning'][] = $warning;
    }

    /**
     * Записать замечание в лог
     */
    protected function setNotice($notice)
    {
        $this->log['notice'][] = $notice;
    }

    /**
     * Получение содержимого лога
     * @return array лог с перечнем ошибок, предупреждений и замечаний
     */
    public function getLog()
    {
        return $this->log;
    }

    public function getSQL($queryList)
    {
        if (!$this->driver) {
            $this->setError(SuQLError::DRIVER_NOT_DEFINED);
            return false;
        }

        if ($queryList === 'all')
            $queryList = $this->getFullQueryList();

        if (!is_array($queryList)) return null;

        $classBuilder = SQLDriver::get($this->driver);
        $SQLBuilder = new $classBuilder($this);
        $SQLBuilder->run($queryList);
        $sqlList = $SQLBuilder->getSql($queryList);

        $this->clear();

        return $sqlList;
    }

    public function getQueries()
    {
        return $this->queries;
    }

    public function extend($queries)
    {
        $this->queries = array_merge($this->queries, $queries);
    }

    public function getFullQueryList()
    {
        return array_keys($this->queries);
    }

    public function rel($leftTable, $rightTable, $on, $temporary = false)
    {
        $leftTable = new SuQLTableName($leftTable);
        $rightTable = new SuQLTableName($rightTable);

        if ($leftTable->alias)
            $on = str_replace($leftTable->format("%a."), $leftTable->format("%n."), $on);

        if ($rightTable->alias)
            $on = str_replace($rightTable->format("%a."), $rightTable->format("%n."), $on);

        $this->scheme[$temporary ? 'temp_rel' : 'rel'][$leftTable->name][$rightTable->name] = $on;
        $this->scheme[$temporary ? 'temp_rel' : 'rel'][$rightTable->name][$leftTable->name] = $on;
    }

    public function temp_rel($leftTable, $rightTable, $on)
    {
        return $this->rel($leftTable, $rightTable, $on, $temporary = true);
    }

    public function getRels()
    {
        return array_merge($this->scheme['rel'], $this->scheme['temp_rel']);
    }

    public function hasRelBetween($table1, $table2)
    {
        return isset($this->scheme['rel'][$table1][$table2])
            || isset($this->scheme['temp_rel'][$table1][$table2]);
    }

    public function getRelTypeBetween($table1, $table2)
    {
        if (isset($this->scheme['rel'][$table1][$table2]))
            return 'rel';
        else if (isset($this->scheme['temp_rel'][$table1][$table2]))
            return 'temp_rel';
        else
            return null;
    }

    public function getRelBetween($table1, $table2)
    {
        if ($this->hasRelBetween($table1, $table2))
            return $this->scheme[$this->getRelTypeBetween($table1, $table2)][$table1][$table2];
        else
            return null;
    }

    public function addSelect($name)
    {
        $this->queries[$name] = new SuQLSelect($this);
    }

    public function addInsert($name)
    {
        $this->queries[$name] = new SuQLInsert($this);
    }

    public function addUnion($name, $query)
    {
        $this->queries[$name] = new SuQLUnion($this, $query);
    }

    public function addUnionTable($name, $unionType, $table)
    {
        if (!isset($this->queries[$name]))
            $this->queries[$name] = new SuQLUnion($this, $table);
        else
            $this->queries[$name]->addUnionTable($unionType, $table);
    }

    public function getQuery($name)
    {
        return $this->queries[$name];
    }

    public function hasQuery($name)
    {
        return isset($this->queries[$name]);
    }

    public function getModifierClass($modifierHandler)
    {
        foreach ($this->modifierList() as $modifierClass) {
            if (method_exists($modifierClass, $modifierHandler))
                return $modifierClass;
        }

        return null;
    }

    public function hasParam($param)
    {
        return array_key_exists($param, $this->params);
    }

    public function hasValuableParam($param)
    {
        return $this->hasParam($param) && !is_null($this->params[$param]) && $this->params[$param]->isValuable();
    }

    public function getParam($param)
    {
        return $this->params[$param];
    }

    public function setParam($param, $suqlParam)
    {
        $this->params[$param] = $suqlParam;
    }
}
