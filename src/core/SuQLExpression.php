<?php

namespace suql\core;

/**
 * Выражения с suql\core\SuQLCondition
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class SuQLExpression
{
    /**
     * @var string строка выражения
     */
    private $expression;
    /**
     * @var array набор suql\core\SuQLCondition
     */
    private $conditions;
    /**
     * Constructor
     * @param string $expression
     * @param array $conditions
     */
    function __construct($expression, $conditions)
    {
        $this->expression = $expression;
        $this->conditions = $conditions;
    }
    /**
     * Конвертирует в строку
     * @return string
     */
    public function __toString()
    {
        $expression = $this->expression;

        foreach ($this->conditions as $index => $condition)
        {
            $expression = str_replace('$'.($index+1), $condition, $expression);
        }

        return $expression;
    }
}