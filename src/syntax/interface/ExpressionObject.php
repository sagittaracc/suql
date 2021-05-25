<?php

namespace suql\syntax;

/**
 * Объект выражения
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
interface ExpressionObject
{
    /**
     * Дожно быть объявлено выражение
     * @return string
     */
    public static function expression();
    /**
     * Должны быть объявлены условия
     * @return array
     */
    public static function conditions();
}