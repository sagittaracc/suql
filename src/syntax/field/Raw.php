<?php

namespace suql\syntax\field;

/**
 * Сырые sql выражения
 * Пример:
 * Raw::expression("CONCAT(@Name, ' (', FORMAT(@Price, 0), ' р.)') AS tarif"),
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
     * @param string $expression
     */
    function __construct(string $expression)
    {
        $this->expression = $expression;
    }
    /**
     * Создает сырое sql выражение
     * @param string $expression
     * @return self
     */
    public static function expression(string $expression)
    {
        return new self($expression);
    }
    /**
     * Получает сырое sql выражение
     * @return string
     */
    public function getExpression(): string
    {
        return $this->expression;
    }
}
