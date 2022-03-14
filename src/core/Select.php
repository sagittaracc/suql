<?php

namespace suql\core;

use suql\core\SelectQueryInterface;

/**
 * Объект хранящий структуру select запроса
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Select extends Query implements SelectQueryInterface, Buildable
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
     * @var string алиас таблицы из которой происходит выборка
     */
    private $alias = null;
    /**
     * @var array список условий where
     */
    private $where = [];
    /**
     * @var array WHERE 2.0 =) То как список where должен был выглядеть изначально
     * TODO: Весь перечень из $this->where перенести в этот список и переименовать
     */
    private $where20 = [];
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
     * @inheritdoc
     */
    public function getBuilderFunction()
    {
        return 'buildSelectQuery';
    }
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
     * @param boolean $raw флажок - сырое поле или нет
     * @return suql\core\FieldName
     */
    public function addField($table, $name, $visible = true, $raw = false)
    {
        $tablePlaceholderName = $table;

        if ($this->getOSuQL()->getScheme()->hasTableAlias($table)) {
            $tablePlaceholderName = $this->getOSuQL()->getScheme()->getTableAlias($table);
        }

        if ($tablePlaceholderName === $this->from && $this->alias) {
            $table = $this->alias;
        }

        $field = new FieldName($table, $name);

        $this->select[] = new Field($this, $table, $field, $visible, $raw);

        return $field;
    }
    /**
     * Добавить сырое выражение
     * @param string $expression
     * @param boolean $visible
     */
    public function addRaw($expression, $visible = true)
    {
        $this->addField(null, $expression, $visible, true);
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
            if ($ofield->getField() === $field->format('%t.%n') && $ofield->getAlias() === $field->alias) {
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
        if ($ofield = $this->hasField($table, $name)) {
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

        foreach ($this->select as $ofield) {
            $fieldList[$ofield->getField()] = $ofield->getAlias();
        }

        return $fieldList;
    }
    /**
     * Добавляет таблицу from секции
     * @param mixed $table имя таблицы
     */
    public function addFrom($table)
    {
        $tableFrom = new TableName($table);
        $table = $tableFrom->name;
        $alias = $tableFrom->alias;

        if ($this->getOSuQL()->getScheme()->hasTableAlias($table)) {
            $table = $this->getOSuQL()->getScheme()->getTableAlias($table);
        }

        $this->from = $table;
        $this->alias = $alias;
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
     * Получает алиас секции from
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }
    /**
     * Добавляет выражение
     * @param string|Expression $expression
     * @param array $stack
     */
    private function addExpression($expression, &$stack)
    {
        if (!$expression) return;

        if (is_string($expression)) {
            $this->addStringWhere($expression, $stack);
        } else if ($expression instanceof Expression) {
            $this->addExpressionWhere($expression, $stack);
        } else if ($expression instanceof Condition) {
            $this->addConditionWhere($expression, $stack);
        }
    }
    /**
     * Добавляет условие where из строки
     * @param string $expression
     * @param array $stack
     */
    private function addStringWhere($expression, &$stack)
    {
        $stack[] = $expression;
    }
    /**
     * Добавляет условие where из expression
     * @param suql\core\Expression $expression
     * @param array $stack
     */
    private function addExpressionWhere($expression, &$stack)
    {
        $stack[] = $expression->getExpression();

        foreach ($expression->getParams() as $param => $value) {
            $this->getOSuQL()->setParam($param, $value);
        }
    }
    /**
     * Добавляем простой condition
     * @param suql\core\Condition $expression
     * @param array $stack
     */
    private function addConditionWhere($expression, &$stack)
    {
        $stack[] = $expression->getCondition();

        foreach ($expression->getParams() as $param => $value) {
            $this->getOSuQL()->setParam($param, $value);
        }
    }
    /**
     * Добавляет условие where
     * @param string $where
     */
    public function addWhere($where)
    {
        $this->addExpression($where, $this->where);
    }
    /**
     * Добавляет условие where 2.0
     * @param suql\core\FieldName $fieldName
     * @param mixed $condition
     */
    public function addWhere20($fieldName, $condition)
    {
        $this->where20[] = [
            'fieldName' => $fieldName,
            'condition' => $condition,
        ];
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
     * Возвращает текущий список всех where условий 2.0
     * @return array
     */
    public function getWhere20()
    {
        return $this->where20;
    }
    /**
     * Добавляет условие having
     * @param string $having
     */
    public function addHaving($having)
    {
        if ($having) {
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
     * @param mixed $table название таблицы
     */
    public function addJoin($type, $table)
    {
        $tableFrom = new TableName($table);
        $table = $tableFrom->name;
        $alias = $tableFrom->alias;

        if ($this->getOSuQL()->getScheme()->hasTableAlias($table)) {
            $table = $this->getOSuQL()->getScheme()->getTableAlias($table);
        }

        $this->join[] = new Join($this, $table, $type, $alias);
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
     * Получает последний join
     * @return suql\core\Join
     */
    public function getLastJoin()
    {
        return end($this->join);
    }
    /**
     * Выполняет автоматическую цепочку join'ов от начальной таблицы до конечной
     * @param string $fromTable начальная таблица
     * @param string $toTable конечная таблица
     * @param string $type тип join
     */
    public function addSmartJoin($fromTable, $toTable, $type = 'inner')
    {
        $scheme = $this->getOSuQL()->getScheme();

        if ($scheme->hasTableAlias($fromTable)) {
            $fromTable = $scheme->getTableAlias($fromTable);
        }

        if ($scheme->hasTableAlias($toTable)) {
            $toTable = $scheme->getTableAlias($toTable);
        }

        $smartJoin = new SmartJoin($this, $fromTable, $toTable, $type);
        $joinChain = $smartJoin->getChain();
        array_shift($joinChain);

        foreach ($joinChain as $table) {
            if (in_array($table, $this->table_list)) continue;
            $this->addJoin($type, $table);
        }
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
        if ($offset) {
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
        if ($limit) {
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
