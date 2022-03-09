<?php

namespace suql\core;

/**
 * Управление автоматическим сцеплением сущностей в базе данных
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Join
{
    /**
     * @var suql\core\Select
     */
    private $oselect = null;
    /**
     * @var string название таблицы которая цепляется
     */
    private $table;
    /**
     * @var string алиас таблицы которая цепляется
     */
    private $alias;
    /**
     * @var string тип join (inner, left, right, cross, etc.)
     */
    private $type;
    /**
     * @var string условие сцепления
     */
    private $on;
    /**
     * Constructor
     * @param suql\core\Select
     * @param string $table название таблицы
     * @param string $type тип join
     * @param string $alias алиас таблицы
     */
    function __construct($oselect, $table, $type, $alias = '')
    {
        $this->oselect = $oselect;
        $this->table = $table;
        $this->alias = $alias;
        $this->type = $type;
        $this->on = $this->getLink();
    }
    /**
     * Автоматическое определение возможного сцепления таблицы self::$table с ранее объявленными
     * @return string условие сцепление
     */
    private function getLink()
    {
        $scheme        = $this->oselect->getOSuQL()->getScheme()->getRels();
        $tableList     = $this->oselect->getTableList();
        $tableLinks    = array_keys($scheme[$this->table]);
        $possibleLinks = array_intersect($tableLinks, $tableList);
        $targetLink    = array_pop($possibleLinks);
        $on            = $scheme[$this->table][$targetLink];

        return $on;
    }
    /**
     * Возвращает имя таблицы
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }
    /**
     * Возвращается алиас таблицы
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }
    /**
     * Возвращает тип join
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * Возвращает условие сцепления
     * @return string
     */
    public function getOn()
    {
        return $this->on;
    }
    /**
     * Устанавливает условие сцепления
     * @param string $on
     */
    public function setOn($on)
    {
        $this->on = $on;
    }
}
