<?php

namespace suql\syntax\field;

/**
 * Модификация поля
 * 
 * Например:
 * Model::all()
 *         ->select([
 *             ...,
 *             new Field([<field> => <alias>], [
 *                 <modifier-1>,
 *                 <modifier-2> => <params>,
 *                 ...
 *             ]),
 *             ...
 *         ]),
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Field
{
    /**
     * @var string название поля
     */
    private $field;
    /**
     * @var array список модификаторов
     */
    private $modifiers;
    /**
     * Constructor
     * @param string|array $field имя поля
     * @param array $modifiers список модификаторов
     */
    function __construct($field, $modifiers = [])
    {
        $this->field = $field;
        $this->modifiers = $modifiers;
    }
    /**
     * Получает полю к которому применялся модификатор
     * @return string|array
     */
    public function getField()
    {
        return $this->field;
    }
    /**
     * Получить список модификаторов
     * @return array
     */
    public function getModifiers()
    {
        return $this->modifiers;
    }
    /**
     * Сырое выражение
     * @param string сырое выражение
     */
    public static function raw($expression)
    {
        return Raw::expression($expression);
    }
}