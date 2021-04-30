<?php

namespace suql\syntax;

/**
 * Класс обработчик хранимых процедур
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class SuQLProcedure extends SuQLFunction implements SuQLProcedureInterface
{
    /**
     * Создание экземпляра класса хранимой процедуры
     * @return self
     */
    public static function find($name = null)
    {
        $procedure = parent::proc($name);
        return $procedure;
    }
}
