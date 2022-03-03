<?php

namespace suql\core;

/**
 * Управление хранимыми процедурами.
 * Хранимая процедура - это просто хранимая функция не возвращающая результат.
 * На данный момент ничем не отличаются от хранимых функций за исключением реализуемого интерфейса для итоговой сборки в чистый SQL
 */
class Proc extends Func implements ProcedureQueryInterface, Buildable
{
    /**
     * @inheritdoc
     */
    public function getBuilderFunction()
    {
        return 'buildStoredProcedure';
    }
}
