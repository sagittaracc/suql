<?php

namespace suql\modifier\field;

/**
 * Модификатор as
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class SQLAsModifier extends SQLBaseModifier
{
    /**
     * As модификатор
     * Дополнительный вариант задания алиаса для поля
     * @param suql\core\Field объект поля по которому выполняется группировка
     * @param array $param не используется в данном модификаторе
     */
    public static function mod_as($ofield, $params)
    {
        $ofield->setAlias($params[0]);
    }
}
