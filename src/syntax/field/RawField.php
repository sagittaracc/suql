<?php

namespace suql\syntax\field;

/**
 * Сырые sql выражения
 * Пример:
 * RawField::expression("CONCAT(@Name, ' (', FORMAT(@Price, 0), ' р.)') AS tarif"),
 * Символ @ - будет заменен при сборке запроса на текущую используемую таблицу
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Raw
{
    /**
     * @var string сырое sql выражение
     */
    private $expression;
    /**
     * Constructor
     */
    function __construct($expression)
    {
        $this->expression = $expression;
    }
    /**
     * Создает сырое sql выражение
     * @param string $expression
     * @return self
     */
    public static function expression($expression)
    {
        return new static($expression);
    }
    /**
     * Получает сырое sql выражение
     * @return string
     */
    public function getExpression()
    {
        return $this->expression;
    }
}