<?php

namespace suql\core;

/**
 * Выражения с suql\core\Condition
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Expression
{
    /**
     * @var string строка выражения
     */
    private $expression;
    /**
     * @var array набор suql\core\Condition
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
    public function getExpression()
    {
        $expression = $this->expression;

        foreach ($this->conditions as $index => $condition)
        {
            $conditionIndex = $index + 1;
            $expression = str_replace("$$conditionIndex", $condition->getCondition(), $expression);
        }

        return $expression;
    }
    /**
     * Возвращает перечень параметров
     * @return array
     */
    public function getParams($onlyValuable = false)
    {
        $paramList = [];

        foreach ($this->conditions as $condition)
        {
            if ($onlyValuable && !$condition->isValuable()) continue;

            $paramList = array_merge($paramList, $condition->getParams());
        }

        return $paramList;
    }
}