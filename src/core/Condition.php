<?php

namespace suql\core;

/**
 * Простое условное выражение типа <field> <condition> <value>
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Condition
{
    /**
     * @var suql\core\Param
     */
    private $param;
    /**
     * @var string условное выражение
     */
    private $condition;
    /**
     * @var string формат вывода поля
     */
    private $format;
    /**
     * Constructor
     * @param suql\core\Param $param
     * @param string $condition условное выражение
     * @param string $format формат вывода поля
     */
    function __construct($param, $condition, $format = '%n')
    {
        $this->param = $param;
        $this->condition = $condition;
        $this->format = $format;
    }
    /**
     * Установить формат вывода поля
     * @param string $format
     */
    public function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }
    /**
     * Конвертирует в строку
     * @return string
     */
    public function getCondition()
    {
        return str_replace(
            [
                '$',
                '?',
            ],
            [
                $this->param->getField()->format($this->format),
                $this->param->getPlaceholder(),
            ],
            $this->condition
        );
    }
    /**
     * Получает параметры
     * @return array
     */
    public function getParams()
    {
        return $this->param->getParamList();
    }
    /**
     * Valuable condition
     * @return boolean
     */
    public function isValuable()
    {
        return $this->param->isValuable();
    }
}
