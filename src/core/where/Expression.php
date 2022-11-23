<?php

namespace suql\core\where;

use suql\core\FieldName;

/**
 * Example:
 * 
 * Expression::create('$1 and ($2 or $3)')
 *     ->addCondition(new FieldName('t1', 'f1'), Equal::integer(1))
 *     ->addCondition(new FieldName('t1', 'f2'), Greater::integer(2))
 *     ->addCondition(new FieldName('t1', 'f3'), Less::integer(3))
 * 
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

    public static function string(string $expression)
    {
        return new static($expression);
    }
}