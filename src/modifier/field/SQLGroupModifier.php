<?php

namespace suql\modifier\field;

/**
 * Модификатор group by
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class SQLGroupModifier
{
    /**
     * Group by
     * @param suql\core\SuQLField объект поля по которому выполняется группировка
     * @param array $param не используется в данном модификаторе
     */
    public static function mod_group($ofield, $params)
    {
        $ofield->getOSelect()->addGroup($ofield->getField());
    }
}
