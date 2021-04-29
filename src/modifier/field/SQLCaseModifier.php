<?php

namespace suql\modifier\field;

use ReflectionClass;
use sagittaracc\PlaceholderHelper;
use suql\core\SuQLCondition;
use suql\core\SuQLExpression;
use suql\core\SuQLFieldName;

/**
 * Модификатор case when
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class SQLCaseModifier
{
    /**
     * Основной обработчик перечня case условий
     * @param array $case перечень case условий
     * @param suql\core\SuQLField $ofield объект поля к которому применяется модификатор
     * @param array $params не используется в данном модификаторе
     */
    private static function case($case, $ofield, $params)
    {
        $caseList = [];

        foreach ($case as $list) {
            $when = $list[0];
            $then = $list[1];

            if ($when === 'default')
            {
                $caseList[] = (new PlaceholderHelper("else ?"))->bind($then);
            }
            else if ($when instanceof SuQLExpression || $when instanceof SuQLCondition)
            {
                $caseList[] = "when $when " . (new PlaceholderHelper("then ?"))->bind($then);
            }
        }

        $ofield->setField('case ' . implode(' ', $caseList) . ' end');
    }
    /**
     * Case expression
     * @param array $case 
     */
    private static function expression($case)
    {
        foreach ($case as $key => $value)
        {
            if ($key === 0)
            {
                $then = $value;
            }
            else
            {
                $expression = $key;
                $list = [];
                foreach ($value as $cond)
                {
                    $reflector = new ReflectionClass(SuQLCondition::class);
                    $list[] = $reflector->newInstanceArgs($cond);
                }
            }
        }
        return [new SuQLExpression($expression, $list), $then];
    }
    /**
     * Case condition
     * @param array $case
     */
    private static function condition($case)
    {
        return [new SuQLCondition($case[0][0], $case[0][1]), $case[1]];
    }
    /**
     * Парсер case условий
     * @param array $caseList
     * @param suql\core\SuQLField $ofield
     * @param array $params
     */
    public static function parse($caseList, $ofield, $params)
    {
        $list = [];
        foreach ($caseList as $case)
        {
            if (is_string($case[0]))
            {
                if (count($case) === 1)
                {
                    $list[] = ['default', $case[0]];
                }
                else
                {
                    $list[] = self::expression($case);
                }
            }
            else if (is_array($case[0]))
            {
                $list[] = self::condition($case);
            }
        }
        self::case($list, $ofield, $params);
    }
    /**
     * Тестовый case модификатор
     */
    public static function mod_test_case($ofield, $params)
    {
        $table = $ofield->getTable();
        $field = $ofield->getField();
        $fieldName = new SuQLFieldName($table, $field);
        $anotherFieldName = new SuQLFieldName('groups', 'id');
        
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