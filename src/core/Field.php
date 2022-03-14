<?php

namespace suql\core;

/**
 * Объект обработки полей запроса
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Field
{
    /**
     * @var siql\core\Select Ссылка на основной объект выборки
     */
    private $oselect = null;
    /**
     * @var string название таблицы которой принадлежит это поле
     */
    private $table;
    /**
     * @var string название поля
     */
    private $name;
    /**
     * @var string название поля (полное с таблицей если таблица указана)
     */
    private $field;
    /**
     * @var suql\core\FieldName поле с его настройками
     * Это поле впоследствии должно избавить класс от полей $table, $name, $alias
     * TODO: Потом переименовть в $field когда выпилится $table, $name, $alias
     */
    private $field2;
    /**
     * @var string алиас поля если указан
     */
    private $alias;
    /**
     * @var boolean флаг говорящий выводить это поле в выборке или нет
     * Иногда необходимо использовать поле только в служебных целях
     * например чтобы применить модификатор
     */
    private $visible;
    /**
     * @var boolean сырое поле или нет
     */
    private $raw;
    /**
     * @var array перечень модификаторов которые будут применяться к полю
     */
    private $modifier = [];
    /**
     * Constructor
     * @param suql\core\Select ссылка на основной объект выборки
     * @param string $table название таблицы
     * @param suql\core\FieldName $field объект с параметрами поля
     * @param boolean $visible добавить поле в выборку или нет
     * @param boolean $raw флажок - сырое поле или нет
     */
    function __construct($oselect, $table, $field, $visible, $raw = false)
    {
        $this->oselect = $oselect;
        $this->table = $table;
        $this->name = $field->name;
        $this->field = $table ? "$field->table.$field->name" : $field->name;
        $this->alias = $field->alias;
        $this->field2 = $field;
        $this->visible = $visible;
        $this->raw = $raw;
    }
    /**
     * Вернуть ссылку на основной объект выборки
     * @return suql\core\Select
     */
    public function getOSelect()
    {
        return $this->oselect;
    }
    /**
     * Вернуть название таблицы
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }
    /**
     * Вернуть название поля
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * Вернуть название поля
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }
    /**
     * Вернуть поле
     * TODO: Переименовать в getField когда избавимся от полей $table, $name, $alias
     * @return suql\core\FieldName
     */
    public function getField2()
    {
        return $this->field2;
    }
    /**
     * Установить новое значение поле
     * Например после применения модификатора
     * @param string $field новое значение поля
     * @return string
     */
    public function setField($field)
    {
        $this->field = $field;
    }
    /**
     * Проверяет задан ли у поля алиас
     * @return boolean
     */
    public function hasAlias()
    {
        return !empty($this->alias);
    }
    /**
     * Возвращает алиас поля
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }
    /**
     * Возвращает видимое ли поле
     * Добавить ли его в общую выборку
     * @return boolean
     */
    public function visible()
    {
        return $this->visible === true;
    }
    /**
     * Добавить модификатор полю
     * @param string $name название модификатора
     * @param array $params параметры модификатора
     */
    public function addModifier($name, $params = [])
    {
        $this->modifier[$name] = $params;
    }
    /**
     * Добавить инлайновый модификатор (типа callback Closure) полю
     * @param Closure $callback инлайновый модификатор-обработчик
     */
    public function addCallbackModifier($callback)
    {
        $this->modifier["callback"] = $callback;
    }
    /**
     * Проверяет заданы ли у поля какие-либо модификаторы
     * @return boolean
     */
    public function hasModifier()
    {
        return !empty($this->modifier);
    }
    /**
     * Получить перечень заданных для поля модификаторов
     * @return array перечень модификаторов
     */
    public function getModifierList()
    {
        return $this->modifier;
    }
    /**
     * Сырое поле или нет
     * @return boolean
     */
    public function isRaw()
    {
        return $this->raw;
    }
}
