<?php

namespace suql\core;

use suql\core\SelectQueryInterface;

/**
 * Объект хранящий структуру select запроса
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Select extends Query implements SelectQueryInterface
{
    /**
     * @var array перечень suql\core\Field полей учавствующих в выборке
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
     * @var array список suql\core\Join объектов описывающих соединение таблиц
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
     * @var array список таблиц учавствующих в запросе. Необходимо для выполнения автоматического join
     */
    private $table_list  = [];
    /**
     * Получить перечень suql\core\Field учавствующий в запросе.
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
     * @return suql\core\FieldName
     */
    public function addField($table, $name, $visible = true)
    {
        $field = new FieldName($table, $name);

        $this->select[] = new Field(
            $this,
            $table,
            $field,
            $field->format('%a'),
            $visible
        );

        return $field;
    }
    /**
     * Добавить сырое выражение
     * @param string $expression
     * @param boolean $visible
     */
    public function addRaw($expression, $visible = true)
    {
        $this->addField(null, $expression, $visible);
    }
    /**
     * Проверяет есть ли поле в текущей выборке
     * @param string $table имя таблицы
     * @param string|array имя поля в трех возможных форматах описанных ранее
     * @return suql\core\Field|false возвращает объект поля если найдено
     */
    public function hasField($table, $name)
    {
        $field = new FieldName($table, $name);

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
     * @return suql\core\Field|null возвращает null если не найдено
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
        if (!$where) return;

        if (is_string($where))
        {
            $this->addStringWhere($where);
        }
        else if ($where instanceof Expression)
        {
            $this->addExpressionWhere($where);
        }
    }
    /**
     * Добавляет условие where из строки
     * @param string $where
     */
    private function addStringWhere($where)
    {
        $this->where[] = $where;
    }
    /**
     * Добавляет условие where из expression
     * @param suql\core\Expression
     */
    private function addExpressionWhere($where)
    {
        $this->where[] = $where->getExpression();

        foreach ($where->getParams() as $param => $value)
        {
            $this->getOSuQL()->setParam($param, $value);
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
     * Добавляет фильтровое условие where. Не применяется если фильтр пустой.
     * Пустота фильтра определяется для каждого типа фильтра отдельно.
     * @param string $filter название фильтра
     * @param string $where условие where
     */
    public function addFilterWhere($filter, $where)
    {
        $this->filterWhere[$filter] = $where;
    }
    /**
     * Получить перечень фильтровых условий where
     * @return array
     */
    public function getFilterWhere()
    {
        return $this->filterWhere;
    }
    /**
     * Добавляет условие having
     * @param string $having
     */
    public function addHaving($having)
    {
        if ($having)
        {
            $this->having[] = $having;
        }
    }
    /**
     * Получает перечень условий секции having
     * @return array
     */
    public function getHaving()
    {
        return $this->having;
    }
    /**
     * Добавляет таблицу в цепочку join в рамках этого запроса
     * @param string $type [left|right|inner|cross|...]
     * @param string $table название таблицы
     */
    public function addJoin($type, $table)
    {
        $this->join[] = new Join($this, $table, $type);
        $this->table_list[] = $table;
    }
    /**
     * Получить цепочку join
     * @return array
     */
    public function getJoin()
    {
        return $this->join;
    }
    /**
     * Добавляет поле в группировку
     * @param string $field название поля
     */
    public function addGroup($field)
    {
        $this->group[] = $field;
    }
    /**
     * Получить перечень полей учавствующих в группировке
     * @return array
     */
    public function getGroup()
    {
        return $this->group;
    }
    /**
     * Добавляет поле в сортировку
     * @param string $field название поля
     * @param string $direction направление сортировки [desc|asc]
     */
    public function addOrder($field, $direction = 'asc')
    {
        $this->order[] = new Order($field, $direction);
    }
    /**
     * Получить перечень полей в сортировке
     * @param array
     */
    public function getOrder()
    {
        return $this->order;
    }
    /**
     * Добавляет модификатор для запроса. Пока работает только distinct
     * @param string $modifier название модификатора
     */
    public function addModifier($modifier)
    {
        $this->modifier = $modifier;
    }
    /**
     * Проверяет заданы ли для данного запроса какие-либо модификаторы
     * @return boolean
     */
    public function hasModifier()
    {
        return !is_null($this->modifier);
    }
    /**
     * Получает модификатор примененный для данного запроса
     * @return string
     */
    public function getModifier()
    {
        return $this->modifier;
    }
    /**
     * Добавляет отступ в выборке данных
     * @param int $offset отступ
     */
    public function addOffset($offset)
    {
        if ($offset)
        {
            $this->offset = $offset;
        }
    }
    /**
     * Проверяет производится ли выборка с отступом
     * @return boolean
     */
    public function hasOffset()
    {
        return !is_null($this->offset);
    }
    /**
     * Получает значение отступа в выборке
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }
    /**
     * Ограничивает итоговую выборку определенным количеством записей
     * @param int $limit количество записей которым мы ограничиваем выборку
     */
    public function addLimit($limit)
    {
        if ($limit)
        {
            $this->limit = $limit;
        }
    }
    /**
     * Проверяет задано ли для данного запроса ограничение в итоговой выборке
     */
    public function hasLimit()
    {
        return !is_null($this->limit);
    }
    /**
     * Получает количество записей каким ограничена итоговая выборка
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }
    /**
     * Цепочка таблиц учавствующая в рамках текущего запроса.
     * @return array
     */
    public function getTableList()
    {
        return $this->table_list;
    }
}
