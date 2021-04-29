<?php

namespace suql\modifier\field;

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
    public static function mod_case($case, $ofield, $params)
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
     * Тестовый case модификатор
     */
    public static function mod_test_case($ofield, $params)
    {
        $table = $ofield->getTable();
        $field = $ofield->getField();
        $fieldName = new SuQLFieldName($table, $field);
        $anotherFieldName = new SuQLFieldName('groups', 'id');

        self::mod_case([
            [new SuQLCondition($fieldName, '$ = 1'), 'admin'],
            [new SuQLCondition($fieldName, '$ = 2'), 'user'],
            [
                new SuQLExpression('$1 and $2', [
                    new SuQLCondition($fieldName, '$ > 3'),
                    new SuQLCondition($anotherFieldName, '$ < 10', '%t.%n')
                ]),
                'guest'
            ],
        ], $ofield, $params);
    }
}
