<?php

namespace suql\modifier\field;

/**
 * Модификатор order by
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class SQLOrderModifier
{
    /**
     * Модификатор сортировки asc
     * @param suql\core\SuQLField $ofield объект поля к которому применяется модификатор
     * @param array $params не используется в данном модификаторе
     */
    public static function mod_asc($ofield, $params)
    {
        $ofield->getOSelect()->addOrder($ofield->getFieldName(), 'asc');
    }
    /**
     * Модификатор сортировки desc
     * @param suql\core\SuQLField $ofield объект поля к которому применяется модификатор
     * @param array $params не используется в данном модификаторе
     */
    public static function mod_desc($ofield, $params)
    {
        $ofield->getOSelect()->addOrder($ofield->getFieldName(), 'desc');
    }
}
