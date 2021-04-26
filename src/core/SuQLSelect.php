<?php

namespace suql\core;

use suql\core\SelectQueryInterface;

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
    /**
     * Добавляет новое поле в выборку
     * @param string $table таблица из которой происходит выборка поля
     * @param string|array $name название поля в трех возможных форматах
     *   1. <field> - строка с названием поля
     *   2. [<field> => <alias>] - массив поле и его алиас
     *   3. <field>@<alias> - строка с полем и его алиасом
     * @param boolean $visible некоторые поля нужны просто чтобы применить к ним модификатор
     * например поля сортировки или группировки или фильтрации но их не нужно выводить в результат
     * @return suql\core\SuQLFieldName
     */
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
    /**
     * Проверяет есть ли поле в текущей выборке
     * @param string $table имя таблицы
     * @param string|array имя поля в трех возможных форматах описанных ранее
     * @return suql\core\SuQLField|false возвращает объект поля если найдено
     */
    public function hasField($table, $name)
    {
        $field = new SuQLFieldName($table, $name);

        foreach ($this->select as $ofield) {
            if ($ofield->getField() === $field->format('%t.%n')
             && $ofield->getAlias() === $field->format('%a'))
            {
                return $ofield;
            }
        }

        return false;
    }
    /**
     * Возвращает поле по имени таблицы и имени поля
     * @param string $table имя таблицы
     * @param string|array $name имя поля в трех возможных форматах описанных ранее
     * @return suql\core\SuQLField|null возвращает null если не найдено
     */
    public function getField($table, $name)
    {
        if ($ofield = $this->hasField($table, $name))
        {
            return $ofield;
        }

        return null;
    }
    /**
     * Возвращает текущий перечень полей в выборке
     * @return array [
     *   <field_1> => <alias_1>,
     *   <field_2> => <alias_2>,
     *   ...
     * ]
     */
    public function getFieldList()
    {
        $fieldList = [];

        foreach ($this->select as $ofield)
        {
            $fieldList[$ofield->getField()] = $ofield->getAlias();
        }

        return $fieldList;
    }
    /**
     * Добавляет таблицу from секции
     * @param string $table имя таблицы
     */
    public function addFrom($table)
    {
        $this->from = $table;
        $this->table_list[] = $table;
    }
    /**
     * Получает таблицу секции from
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }
    /**
     * Добавляет условие where
     * @param string $where
     */
    public function addWhere($where)
    {
        if ($where)
        {
            $this->where[] = $where;
        }
    }
    /**
     * Возвращает текущий список всех where условий
     * @return array
     */
    public function getWhere()
    {
        return $this->where;
    }
    /**
     * TODO: Проверить возможно @param $filter не используется
     */
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
