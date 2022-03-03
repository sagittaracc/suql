<?php

namespace suql\core;

/**
 * Реализация именнованной связи
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
interface INamedRel
{
    /**
     * Описание левой таблицы
     * @return array
     * 
     * Example: [leftTableName => leftTableAlias]
     */
    public function leftTable();
    /**
     * Описание правой таблицы
     * @return array
     * 
     * Example: [leftTableName => leftTableAlias]
     */
    public function rightTable();
    /**
     * Описание связи
     * @return string
     * 
     * Example: leftTableAlias.id = rightTableAlias.id
     */
    public function on();
}
