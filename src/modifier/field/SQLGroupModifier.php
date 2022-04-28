<?php

namespace suql\modifier\field;

/**
 * Модификатор group by
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class SQLGroupModifier extends SQLBaseModifier
{
    /**
     * Group by
     * @param suql\core\Field объект поля по которому выполняется группировка
     * @param array $param не используется в данном модификаторе
     */
    public static function mod_group($ofield, $params)
    {
        $ofield->getOSelect()->addGroup($ofield->getField());
    }
}
