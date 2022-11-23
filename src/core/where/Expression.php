<?php

namespace suql\core\where;

use suql\core\FieldName;

/**
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Expression
{
    private $expression;

    private $conditions;

    function __construct(string $expression)
    {
        $this->expression = $expression;
        $this->conditions = [];
    }

    public function addCondition(FieldName $fieldName, Condition $condition)
    {
        $this->conditions[] = [
            'fieldName' => $fieldName,
            'condition' => $condition,
        ];
        return $this;
    }

    public function getConditions()
    {
        return $this->conditions;
    }

    public static function string(string $expression)
    {
        return new static($expression);
    }

    public function getExpression()
    {
        return $this->expression;
    }
}