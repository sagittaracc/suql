<?php

namespace suql\core;

/**
 * Объект простого запроса хранящий ссылку на основной объект core\suql\SuQLObject
 * Связующее звено конкретных реализаций запросов с основным объектом.
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
abstract class SuQLQuery
{
    /**
     * @var suql\core\SuQLObject ссылка на основной объект
     */
    protected $osuql = null;
    /**
     * Constructor
     * @param suql\core\SuQLObject $osuql ссылка на основной объект
     */
    function __construct($osuql)
    {
        $this->osuql = $osuql;
    }
    /**
     * Получает ссылку на основной объект
     * @return suql\core\SuQLObject
     */
    public function getOSuQL()
    {
        return $this->osuql;
    }
}
