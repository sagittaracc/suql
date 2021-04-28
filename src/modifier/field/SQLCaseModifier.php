<?php

namespace suql\modifier\field;

use sagittaracc\PlaceholderHelper;

/**
 * Модификатор case when
 * Пример:
 * 
 * public static function mod_role($ofield, $params) {
 *     self::mod_case([
 *         (new SuQLModifier('between', [1, 2]))->applyTo($ofield->getField()) => new SuQLFieldName(<table>, <name>)
 *         '$ = 1'   => 'admin',
 *         '$ = 2'   => 'user',
 *         '$ = 3'   => 'guest',
 *         'default' => '',
 *     ], $ofield, $params);
 * }
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
        $fieldName = $ofield->getField();
        $caseList = [];

        foreach ($case as $when => $then) {
            if ($when === 'default') {
                $caseList[] = (new PlaceholderHelper("else ?"))->bind($then);
            } else {
                $caseList[] = "when " . str_replace('$', $fieldName, $when) . (new PlaceholderHelper(" then ?"))->bind($then);
            }
        }

        $ofield->setField('case ' . implode(' ', $caseList) . ' end');
    }
}
