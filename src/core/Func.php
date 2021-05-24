<?php

namespace suql\core;

use sagittaracc\PlaceholderHelper;

/**
 * Управление хранимыми функциями
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Func extends Query implements FunctionQueryInterface
{
    /**
     * @var string название хранимой функции
     */
    private $name;
    /**
     * @var array параметры хранимой функции
     */
    private $params;
    /**
     * Constructor
     * @param suql\core\Obj $osuql ссылка на основной объект структуры запроса
     * @param string $name название хранимой процедуры
     */
    function __construct($osuql, $name)
    {
        parent::__construct($osuql);
        $this->name = $name;
    }
    /**
     * Добавление параметров в хранимую функцию
     * @param array $param список параметров
     */
    public function addParams($params)
    {
        foreach ($params as $param)
        {
            if ($param instanceof Placeholder)
            {
                $this->params[] = $param->getPlaceholder();
            }
            else
            {
                $this->params[] = (new PlaceholderHelper("?"))->bind($param);
            }
        }
    }
    /**
     * Получает название хранимой функции
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * Получает список параметров
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
}