<?php

namespace suql\core;

use suql\core\interface\SelectQueryInterface;

/**
 * Объект хранящий структуру select запроса
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class SuQLSelect extends SuQLQuery implements SelectQueryInterface
{
    /**
     * @var array перечень suql\core\SuQLField полей учавствующих в выборке
     */
    private $select = [];
    /**
     * @var string таблица/вьюха из которой происходит выборка
     */
    private $from = null;
    /**
     * @var array список условий where
     */
    private $where = [];
    /**
     * @var array список условий для фильтрации не будут применяться если фильтры пустые
     */
    private $filterWhere = [];
    /**
     * @var array список условий having
     */
    private $having = [];
    /**
     * @var array список suql\core\SuQLJoin объектов описывающих соединение таблиц
     */
    private $join = [];
    /**
     * @var array список полей по которым выполняется группировка
     */
    private $group = [];
    /**
     * @var array список полей по которым выполняется сортировка
     */
    private $order = [];
    /**
     * @var string модификатор запроса (пока работает только distinct)
     */
    private $modifier = null;
    /**
     * @var integer $offset
     */
    private $offset = null;
    /**
     * @var integer $limit
     */
    private $limit = null;
    /**
     * @var array список таблиц учавствующих в запросе
     */
    private $table_list  = [];
    /**
     * Получить перечень suql\core\SuQLField учавствующий в запросе
     * @return array
     */
    public function getSelect()
    {
        return $this->select;
    }

    public function addField($table, $name, $visible = true)
    {
        $field = new SuQLFieldName($table, $name);
        $this->select[] = new SuQLField(
            $this,
            $table,
            $field,
            $field->format('%a'),
            $visible
        );
        return $field;
    }

    public function hasField($table, $name)
    {
        $field = new SuQLFieldName($table, $name);

        foreach ($this->select as $ofield) {
            if (
                $ofield->getField() === $field->format('%t.%n')
                && $ofield->getAlias() === $field->format('%a')
            )
                return $ofield;
        }

        return false;
    }

    public function getField($table, $name)
    {
        if ($ofield = $this->hasField($table, $name))
            return $ofield;

        return null;
    }

    public function getFieldList()
    {
        $fieldList = [];

        foreach ($this->select as $ofield) {
            $fieldList[$ofield->getField()] = $ofield->getAlias();
        }

        return $fieldList;
    }

    public function addFrom($table)
    {
        $this->from = $table;
        $this->table_list[] = $table;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function addWhere($where)
    {
        if ($where)
            $this->where[] = $where;
    }

    public function getWhere()
    {
        return $this->where;
    }

    public function addFilterWhere($filter, $where)
    {
        $this->filterWhere[$filter] = $where;
    }

    public function getFilterWhere()
    {
        return $this->filterWhere;
    }

    public function addHaving($having)
    {
        if ($having)
            $this->having[] = $having;
    }

    public function getHaving()
    {
        return $this->having;
    }

    public function addJoin($type, $table)
    {
        $this->join[] = new SuQLJoin($this, $table, $type);
        $this->table_list[] = $table;
    }

    public function getJoin()
    {
        return $this->join;
    }

    public function addGroup($field)
    {
        $this->group[] = $field;
    }

    public function getGroup()
    {
        return $this->group;
    }

    public function addOrder($field, $direction = 'asc')
    {
        $this->order[] = new SuQLOrder($field, $direction);
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function addModifier($modifier)
    {
        $this->modifier = $modifier;
    }

    public function hasModifier()
    {
        return !is_null($this->modifier);
    }

    public function getModifier()
    {
        return $this->modifier;
    }

    public function addOffset($offset)
    {
        if ($offset)
            $this->offset = $offset;
    }

    public function hasOffset()
    {
        return !is_null($this->offset);
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function addLimit($limit)
    {
        if ($limit)
            $this->limit = $limit;
    }

    public function hasLimit()
    {
        return !is_null($this->limit);
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function getTableList()
    {
        return $this->table_list;
    }
}
