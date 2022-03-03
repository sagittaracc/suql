<?php

namespace suql\core;

/**
 * Именованная связь между таблицами
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
abstract class NamedRel implements INamedRel
{
    /**
     * @var string левая таблица название
     */
    private $leftTableName = null;
    /**
     * @var string левая таблица алиас
     */
    private $leftTableAlias = null;
    /**
     * @var string правая таблица название
     */
    private $rightTableName = null;
    /**
     * @var string правая таблица алиас
     */
    private $rightTableAlias = null;
    /**
     * @var string Условие связи может быть составным
     */
    private $on = null;
    /**
     * Constructor
     * @param string|array $leftTable левая таблица
     * @param string|array $rightTable правая таблица
     * @param string $on условие связи может быть составным
     */
    function __construct()
    {
        if (is_null($this->leftTable()) || is_null($this->rightTable()) || empty($this->on())) {
            return;
        }

        $leftTable = new TableName($this->leftTable());
        $rightTable = new TableName($this->rightTable());

        $this->leftTableName = $leftTable->name;
        $this->rightTableName = $rightTable->name;

        if ($leftTable->alias) {
            $this->leftTableAlias = $leftTable->alias;
        }

        if ($rightTable->alias) {
            $this->rightTableAlias = $rightTable->alias;
        }

        $this->on = $this->on();
    }
}
