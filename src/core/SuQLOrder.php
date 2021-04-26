<?php

namespace suql\core;

/**
 * Управление сортировкой полей
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class SuQLOrder
{
    /**
     * @var string $field название поля
     */
    private $field;
    /**
     * @var string $direction направление сортировки
     */
    private $direction;
    /**
     * Constructor
     * @param string $field название поля
     * @param string $direction направление сортировки
     */
    function __construct($field, $direction)
    {
        $this->field = $field;
        $this->direction = $direction;
    }
    /**
     * Получает название поля
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }
    /**
     * Получает направление сортировки
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }
}
