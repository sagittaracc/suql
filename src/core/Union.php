<?php

namespace suql\core;

use suql\core\UnionQueryInterface;

/**
 * Объект хранящий структуру union запроса
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Union extends Query implements UnionQueryInterface
{
    /**
     * @var string $suql строковое представление union запроса @query1 union [all] @query2 union ...
     */
    private $suql = '';
    /**
     * Constructor
     * @param suql\core\Obj $osuql ссылка на основной объект OSuQL
     * @param string $suql строковое представление union запроса @query1 union [all] @query2 union ...
     */
    function __construct($osuql, $suql)
    {
        parent::__construct($osuql);
        $this->suql = $suql;
    }
    /**
     * Получить union строку запроса
     * @return string
     */
    public function getSuQL()
    {
        return $this->suql;
    }
    /**
     * Задать union строку запроса
     * @param string $suql
     */
    public function setSuQL($suql)
    {
        $this->suql = $suql;
    }
    /**
     * Добавить таблицу или подзапрос к общей строке union запроса
     * @param string|null $unionType может быть all
     * @param string $table таблица или подзапрос в виде @subquery
     */
    public function addUnionTable($unionType, $table)
    {
        $this->suql .= " $unionType $table";
    }
}
