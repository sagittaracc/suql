<?php

namespace suql\builder;

use sagittaracc\ArrayHelper;
use sagittaracc\Map;
use sagittaracc\PlaceholderHelper;
use suql\core\Condition;
use suql\core\SmartDate;
use suql\core\where\Condition as WhereCondition;
use suql\modifier\field\SQLFunctionModifier;

/**
 * Построитель запросов
 *
 * @author sagittaracc <sagittaracc@gmail.com>
 */
abstract class SQLBuilder
{
    /**
     * @var \suql\core\Obj основной объект хранящий всю структуру запроса
     */
    private $osuql = null;
    /**
     * @var array массив преобразованных в sql запросов
     */
    private $sql = [];
    /**
     * @const string шаблон select запроса
     */
    const SELECT_TEMPLATE = "{select}{from}{join}{where}{group}{having}{order}{limit}";
    /**
     * @const string шаблон insert запроса
     */
    const INSERT_TEMPLATE = "insert into {table} ({fields}) values ({values})";
    /**
     * @const
     */
    const UPDATE_TEMPLATE = "update {table} set {values} {where}";
    /**
     * @var string $quote
     */
    protected $quote = '';
    /**
     * @var string $unquote
     */
    protected $unquote = '';
    /**
     * Привязать объект OSuQL
     * @param \suql\core\Obj $osuql основной объект хранящий всю структуру запроса переданный для преобразования в sql
     */
    public function assign($osuql)
    {
        $this->osuql = $osuql;
    }
    /**
     * Чистка сборщика
     */
    public function clear()
    {
        $this->sql = [];
    }
    /**
     * Возвращает уже преобразованные в sql запросы по именам
     * @param array $queryList массив запросов необходимых для преобразования в sql
     * @return string|array возвращает строку если необходим только один запрос
     */
    public function getSql($queryList)
    {
        if (empty($this->sql)) return null;

        $sqlList = ArrayHelper::slice_by_keys($this->sql, $queryList);

        return count($queryList) === 1 && count($sqlList) === 1
            ? reset($sqlList)
            : $sqlList;
    }
    /**
     * Начинает процесс конвертации в sql
     * @param array $queryList массив запросов необходимых для преобразования в sql
     */
    public function run($queryList)
    {
        if (!$this->osuql)
            return;

        $fullQueryList = $this->osuql->getFullQueryList();
        if (empty($fullQueryList))
            return;

        foreach ($fullQueryList as $query) {
            $this->sql[$query] = trim($this->buildQuery($query));
        }

        foreach ($queryList as $query) {
            $this->sql[$query] = $this->composeQuery($query);
        }
    }
    /**
     * Преобразует в sql запрос с переданным имененм
     * @param string $query имя запроса для конвертации
     * @return string
     */
    private function buildQuery($query)
    {
        $osuql = $this->osuql->getQuery($query);
        $builderFunctionName = $osuql->getBuilderFunction();

        return method_exists($this, $builderFunctionName)
            ? $this->$builderFunctionName($query)
            : null;
    }
    /**
     * Конвертирует хранимую процедуру
     * @param string $query имя запроса для конвертации
     * @return string
     */
    private function buildStoredProcedure($query)
    {
        $oproc = $this->osuql->getQuery($query);
        $name = $oproc->getName();
        $params = $oproc->getParams();
        return 'call ' . $name . '(' . implode(',', $params) . ')';
    }
    /**
     * Конвертирует хранимую функцию
     * @param string $query имя запроса для конвертации
     * @return string
     */
    private function buildStoredFunction($query)
    {
        $oproc = $this->osuql->getQuery($query);
        $name = $oproc->getName();
        $params = $oproc->getParams();
        return 'select ' . $name . '(' . implode(',', $params) . ')';
    }
    /**
     * Конвертирует select запрос
     * @param string $query имя запроса для конвертации
     * @return string
     */
    private function buildSelectQuery($query)
    {
        $this->applyModifier($query);

        $selectTemplate = self::SELECT_TEMPLATE;

        $selectTemplate = str_replace('{select}', $this->buildSelect($query), $selectTemplate);
        $selectTemplate = str_replace('{from}', $this->buildFrom($query), $selectTemplate);
        $selectTemplate = str_replace('{join}', $this->buildJoin($query), $selectTemplate);
        $selectTemplate = str_replace('{group}', $this->buildGroup($query), $selectTemplate);
        $selectTemplate = str_replace('{where}', $this->buildWhere($query), $selectTemplate);
        $selectTemplate = str_replace('{having}', $this->buildHaving($query), $selectTemplate);
        $selectTemplate = str_replace('{order}', $this->buildOrder($query), $selectTemplate);
        $selectTemplate = str_replace('{limit}', $this->buildLimit($query), $selectTemplate);

        // TODO: Перенести на более высокий уровень так как замена алиасов нужна будет не только для select запросов
        return (new PlaceholderHelper($selectTemplate))->bindObject(Map::create(array_flip($this->osuql->getScheme()->getTableList())));
    }
    /**
     * Конвертирует union запрос
     * @param string $query имя запроса для конвертации
     * @return string
     */
    private function buildUnionQuery($query)
    {
        return $this->osuql->getQuery($query)->getSuQL();
    }
    /**
     * Конвертирует insert запрос
     * @param string $query имя запроса для конвертации
     * @return string
     */
    private function buildInsertQuery($query)
    {
        $insertTemplate = self::INSERT_TEMPLATE;

        $insertTemplate = str_replace('{table}', $this->osuql->getQuery($query)->getTable(), $insertTemplate);
        $insertTemplate = str_replace('{fields}', $this->osuql->getQuery($query)->getFields(), $insertTemplate);
        $insertTemplate = str_replace('{values}', $this->osuql->getQuery($query)->getValues(), $insertTemplate);

        return $insertTemplate;
    }
    /**
     * Конвертирует update запрос
     * @param string $query
     * @return string
     */
    private function buildUpdateQuery($query)
    {
        $updateTemplate = self::UPDATE_TEMPLATE;
        $whereList20 = $this->osuql->getQuery($query)->getWhere20();

        $updateTemplate = str_replace('{table}', $this->osuql->getQuery($query)->getTable(), $updateTemplate);
        $updateTemplate = str_replace('{values}', $this->osuql->getQuery($query)->getValues(), $updateTemplate);
        $updateTemplate = str_replace('{where}', $this->buildWhere($query), $updateTemplate);

        return $updateTemplate;
    }
    /**
     * Выполняет декомпозицию вложенных запросов
     * @param string $query имя запроса для конвертации
     * @return string
     */
    private function composeQuery($query)
    {
        if (!isset($this->sql[$query]))
            return '';
        $suql = $this->sql[$query];

        preg_match_all('/@(?<name>\w+)/msi', $suql, $subQueries);

        if (empty($subQueries['name']))
            return $suql;
        else {
            foreach ($subQueries['name'] as $subQuery)
                $suql = str_replace("@$subQuery", '(' . $this->composeQuery($subQuery) . ')', $suql);

            return $suql;
        }
    }
    /**
     * Применяет к полям установленные модификаторы
     * @param string $query имя запроса для конвертации
     */
    public function applyModifier($query)
    {
        $oselect = $this->osuql->getQuery($query);

        foreach ($oselect->getSelect() as $field => $ofield) {
            $this->buildSelectField($ofield);
            if ($ofield->hasModifier()) {
                foreach ($ofield->getModifierList() as $name => $params) {
                    if ($name === 'callback' && $params instanceof \Closure) {
                        $params($ofield);
                    } else {
                        $modifierHandler = "mod_$name";
                        $modifierClass = $this->osuql->getModifierClass($modifierHandler);
                        if ($modifierClass) {
                            $modifierClass::$modifierHandler($ofield, $params);
                        }
                        else {
                            SQLFunctionModifier::func($name, $ofield, $params);
                        }
                    }
                }
            }
        }
    }
    /**
     * Сборка поля по имени и таблице
     * @param suql\core\Field ссылка на объект поля
     */
    public function buildSelectField($ofield)
    {
        $table = $ofield->getTable();
        $name = $ofield->getName();
        $quote = $this->quote;
        $unquote = $this->unquote;

        if ($ofield->isRaw()) {
            $quote = $unquote = '';
        }

        if ($name === '*') {
        }
        else {
            $name = $table
                ? "{$quote}$table{$unquote}.{$quote}$name{$unquote}"
                : "{$quote}$name{$unquote}";
        }

        $ofield->setField($name);
    }
    /**
     * Строит секцию select (перечень полей в выборке)
     * @param string $query имя запроса для конвертации
     * @return string
     */
    protected function buildSelect($query)
    {
        $oselect = $this->osuql->getQuery($query);

        $selectList = [];
        foreach ($oselect->getSelect() as $field => $ofield) {
            if ($ofield->visible()) {
                $name = $ofield->getField();
                $alias = $ofield->getAlias();
                $selectList[] = $alias ? "$name as {$this->quote}$alias{$this->unquote}" : $name;
            }
        }

        $selectList = empty($selectList) ? '*' : implode(', ', $selectList);

        return $oselect->hasModifier()
            ? "select {$oselect->getModifier()} $selectList"
            : "select $selectList";
    }
    /**
     * Строит секцию from
     * @param string $query имя запроса для конвертации
     * @return string
     */
    protected function buildFrom($query)
    {
        $from = $this->osuql->getQuery($query)->getFrom();
        $alias = $this->osuql->getQuery($query)->getAlias();

        if (!$from)
            return '';

        return $this->osuql->hasQuery($from)
            ? " from @$from $from"
            : ($alias
                ? " from {$this->quote}$from{$this->unquote} $alias"
                : " from {$this->quote}$from{$this->unquote}"
            );
    }
    /**
     * Построение on в join
     * @param string $leftTable
     * @param string $leftField
     * @param string $rightTable
     * @param string $rightField
     * @return string
     */
    abstract public function buildJoinOn($leftTable, $leftField, $rightTable, $rightField);
    /**
     * Строит секцию join
     * @param string $query имя запроса для конвертации
     * @return string
     */
    protected function buildJoin($query)
    {
        $join = $this->osuql->getQuery($query)->getJoin();

        if (empty($join))
            return '';

        $joinList = [];
        foreach ($join as $ojoin) {
            $table = $ojoin->getTable();
            $alias = $ojoin->getAlias();
            $type = $ojoin->getType();
            $on = $ojoin->getOn();
            $quote = $this->quote;
            $unquote = $this->unquote;

            if ($this->osuql->hasQuery($table)) {
                $table = "@$table $table";
                $quote = $unquote = '';
            }

            $joinList[] = (
                $alias
                    ? "$type join {$quote}$table{$unquote} $alias" . ($on ? " on $on" : '')
                    : "$type join {$quote}$table{$unquote}" . ($on ? " on $on" : '')
            );
        }

        $joinList = ' ' . implode(' ', $joinList);

        return $joinList;
    }
    /**
     * Строит секцию group
     * @param string $query имя запроса для конвертации
     * @return string
     */
    protected function buildGroup($query)
    {
        $group = $this->osuql->getQuery($query)->getGroup();

        if (empty($group))
            return '';

        $group = implode(', ', $group);

        return " group by $group";
    }
    /**
     * Строит секцию where
     * @param string $query имя запроса для конвертации
     * @return string
     */
    protected function buildWhere($query)
    {
        $whereList = $this->osuql->getQuery($query)->getWhere();
        $whereList20 = $this->osuql->getQuery($query)->getWhere20();

        if (empty($whereList) && empty($whereList20))
            return '';

        $fieldList = $this->osuql->getQuery($query)->getFieldList();
        $fields = array_keys($fieldList);
        $aliases = array_values($fieldList);

        foreach ($whereList as &$where) {
            $where = str_replace($aliases, $fields, $where);
        }
        unset($where);

        $extraWhere = [];
        foreach ($whereList20 as $where20) {
            if ($where20['condition'] instanceof WhereCondition) {
                $extraWhere[] = $this->buildCompare($where20['fieldName'], $where20['condition']);
            }
            elseif ($where20['condition'] instanceof SmartDate) {
                $extraWhere[] = $this->buildSmartDate($where20['fieldName'], $where20['condition']);
            }
            elseif ($where20['condition'] instanceof Condition) {
                $extraWhere[] = $this->buildCondition($where20['fieldName'], $where20['condition']);
            }
        }

        $fullWhereList = array_merge($whereList, $extraWhere);
        if (empty($fullWhereList))
            return '';

        $whereList = implode(' and ', $fullWhereList);

        return " where $whereList";
    }
    /**
     * Строит секцию having
     * @param string $query имя запроса для конвертации
     * @return string
     */
    protected function buildHaving($query)
    {
        $having = $this->osuql->getQuery($query)->getHaving();

        if (empty($having))
            return '';

        $having = implode(' and ', $having);
        return " having $having";
    }
    /**
     * Строит секцию order
     * @param string $query имя запроса для конвертации
     * @return string
     */
    protected function buildOrder($query)
    {
        $order = $this->osuql->getQuery($query)->getOrder();

        if (empty($order))
            return '';

        $orderList = [];
        foreach ($order as $oorder) {
            $field = $oorder->getField();
            $direction = $oorder->getDirection();
            $orderList[] = "$field $direction";
        }

        $orderList = implode(', ', $orderList);

        return " order by $orderList";
    }
    /**
     * Строит секцию offset limit
     * @param string $query имя запроса для конвертации
     * @return string
     */
    protected function buildLimit($query)
    {
        $bound = [];
        $oselect = $this->osuql->getQuery($query);

        if ($oselect->hasOffset()) $bound[] = $oselect->getOffset();
        if ($oselect->hasLimit()) $bound[] = $oselect->getLimit();

        if (empty($bound))
            return '';

        $bound = implode(', ', $bound);

        return " limit $bound";
    }
    /**
     * Сборка SmartDate
     * @param \suql\core\FieldName $fieldName
     * @param \suql\core\SmartDate $smartDate
     * @return string
     */
    abstract protected function buildSmartDate($fieldName, $smartDate);
    /**
     * Сборка модели в запрос на create table
     * @param \suql\core\Obj $model
     * @param boolean $temporary временная или нет
     * @return string
     */
    public function buildModel($model, $temporary = false)
    {
        $columnList = [];

        foreach ($model->getColumns() as $name => $column) {
            $columnList[] =
                $this->quote . $name . $this->unquote . ' ' .
                $column->getType($name) . '(' . $column->getLength($name) . ')' .
                (
                    $column->isAutoIncrement()
                        ? ' auto_increment'
                        : ''
                );
        }

        $pkColumn = $model->getPrimaryKeyColumn();
        if (!is_null($pkColumn)) {
            $columnList[] = "primary key ({$this->quote}{$pkColumn->getName()}{$this->unquote})";
        }

        $columnList = implode(', ', $columnList);

        $createTable = $temporary
            ? "create temporary table"
            : "create table";

        return "$createTable {$this->quote}{$model->table()}{$this->unquote} ($columnList);";
    }
    /**
     * Возвращает запрос получения primary key у таблицы
     * @param string $table
     * @return \sagittaracc\QMap
     */
    abstract public function getPrimaryKeyQuery($table);
    /**
     * Проверяет что таблица существует
     * @param string $config конфигурация подключения к базе данных
     * @param string $table имя таблицы
     * @return boolean
     */
    abstract public function tableExistsQuery($config, $table);
    /**
     * Генерация запроса создания временной таблицы
     * @param \suql\syntax\ActiveRecord $model
     */
    abstract public function createTemporaryTable($model);
    /**
     * Загрузить массив в таблицу
     * @param string $table
     * @param array $data
     */
    abstract public function insertIntoTable($table, $data);
    /**
     * Генерация запроса создания представления
     * @param \suql\syntax\ActiveRecord $model
     * @return string
     */
    abstract public function createView($model);
}
