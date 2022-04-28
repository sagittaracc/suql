<?php

namespace suql\modifier\field;

/**
 * Модификатор order by
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class SQLOrderModifier extends SQLBaseModifier
{
    /**
     * Модификатор сортировки asc
     * @param \suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $params не используется в данном модификаторе
     */
    public static function mod_asc($ofield, $params)
    {
        $ofield->getOSelect()->addOrder(self::getFieldNameToOrderBy($ofield), 'asc');
    }
    /**
     * Модификатор сортировки desc
     * @param \suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $params не используется в данном модификаторе
     */
    public static function mod_desc($ofield, $params)
    {
        $ofield->getOSelect()->addOrder(self::getFieldNameToOrderBy($ofield), 'desc');
    }
    /**
     * Получение поля сортировки
     * @param \suql\core\Field $ofield объект поля к которому применяется модификатор
     * @return string имя поля - алиас если он задан или реальное имя поля в противном случае
     */
    private static function getFieldNameToOrderBy($ofield)
    {
        return $ofield->hasAlias() ? $ofield->getAlias() : $ofield->getField();
    }
}
