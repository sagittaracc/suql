<?php

namespace suql\modifier\field;

use ReflectionClass;
use sagittaracc\PlaceholderHelper;
use suql\core\Condition;
use suql\core\Expression;
use suql\core\FieldName;
use suql\core\param\Simple;

/**
 * Модификатор case when
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class SQLCaseModifier extends SQLBaseModifier
{
    /**
     * Основной обработчик перечня case условий
     * @param array $options перечень case условий
     * @param \suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $params не используется в данном модификаторе
     */
    private static function case($options, $ofield, $params)
    {
        $caseList = [];

        foreach ($options as $option) {
            $when = $option[0];
            $then = $option[1];

            $then = (new PlaceholderHelper("?"))->bind($then);

            if ($when === 'default') {
                $caseList[] = "else $then";
            }
            else {
                if ($when instanceof Condition) {
                    $stringWhen = $when->getCondition();
                }
                else if ($when instanceof Expression) {
                    $stringWhen = $when->getExpression();
                }

                $caseList[] = "when $stringWhen then $then";
            }
        }

        $ofield->setField('case ' . implode(' ', $caseList) . ' end');
    }
    /**
     * Простое условие в case
     * @param array $case настройки case условия
     * Формат данного условия следующий (пример):
     * [
     *     [<suql\core\FieldName>, '$ = 1'],
     *     <then>,
     * ]
     * @return array из одного элемента класса suql\core\Condition
     */
    private static function condition($case)
    {
        $when = $case[0];
        $then = $case[1];

        $field = $when[0];
        $condition = $when[1];

        return [new Condition(new Simple($field), $condition), $then];
    }
    /**
     * Составное условие в case
     * @param array $case настройки case условия
     * Формат данного условия следующий (пример):
     * [
     *     '$1 and $2' => [
     *         [<suql\core\FieldName>, '$ > 1'],
     *         [<suql\core\FieldName>, '$ < 3', '%t.%n']
     *     ],
     *     <then>
     * ]
     * @return array из одного элемента класса suql\core\Expression
     */
    private static function expression($case)
    {
        $conditionList = [];

        foreach ($case as $key => $value)
        {
            if ($key === 0)
            {
                $then = $value;
            }
            else
            {
                $expression = $key;
                foreach ($value as $condition)
                {
                    $field = $condition[0];
                    $condition[0] = new Simple($field);

                    $reflector = new ReflectionClass(Condition::class);
                    $conditionList[] = $reflector->newInstanceArgs($condition);
                }
            }
        }

        return [new Expression($expression, $conditionList), $then];
    }
    /**
     * Парсер case условий
     * @param array $options перечень case условий
     * Формат данного перечня следующий (пример):
     * [
     *     // Пример простого условия
     *     [
     *         [<suql\core\FieldName>, '$ = 1'],
     *         <then>,
     *     ],
     *     // Пример составного условия
     *     [
     *         '$1 and $2' => [
     *             [<suql\core\FieldName>, '$ > 1'],
     *             [<suql\core\FieldName>, '$ < 3', '%t.%n']
     *         ],
     *         <then>
     *     ],
     *     [
     *         <default>
     *     ]
     * ]
     * @param \suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $params параметры модификатора
     */
    public static function parse($options, $ofield, $params)
    {
        $caseList = [];

        foreach ($options as $option)
        {
            if (is_string($option[0]))
            {
                if (count($option) === 1)
                {
                    $caseList[] = ['default', $option[0]];
                }
                else
                {
                    $caseList[] = self::expression($option);
                }
            }
            else if (is_array($option[0]))
            {
                $caseList[] = self::condition($option);
            }
        }

        self::case($caseList, $ofield, $params);
    }
    /**
     * Тестовый case модификатор
     */
    public static function mod_test_case($ofield, $params)
    {
        $table = $ofield->getTable();
        $field = $ofield->getField();
        $fieldName = new FieldName($table, $field);
        $anotherFieldName = new FieldName('groups', 'id');
        
        self::parse([
            [
                [$fieldName, '$ = 1'],
                'admin',
            ],
            [
                [$fieldName, '$ = 2'],
                'user',
            ],
            [
                '$1 and $2' => [
                    [$fieldName, '$ > 3'],
                    [$anotherFieldName, '$ < 10', '%t.%n']
                ],
                'guest'
            ],
            [
                'nobody'
            ]
        ], $ofield, $params);
    }
}