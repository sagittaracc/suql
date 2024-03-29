<?php

namespace suql\core;

/**
 * Объект простого запроса хранящий ссылку на основной объект core\suql\Object
 * Связующее звено конкретных реализаций запросов с основным объектом.
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
abstract class Query
{
    /**
     * @var \suql\core\Obj ссылка на основной объект
     */
    protected $osuql = null;
    /**
     * Constructor
     * @param \suql\core\Obj $osuql ссылка на основной объект
     */
    function __construct($osuql)
    {
        $this->osuql = $osuql;
    }
    /**
     * Получает ссылку на основной объект
     * @return \suql\core\Obj
     */
    public function getOSuQL()
    {
        return $this->osuql;
    }
}
