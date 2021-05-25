<?php

namespace suql\syntax;

/**
 * Сырые sql выражения
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