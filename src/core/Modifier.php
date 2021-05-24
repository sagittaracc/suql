<?php

namespace suql\core;

/**
 * Объект модификатор
 * Более читабельный способ применения модификатора к полю в выборке
 * 
 * Например:
 * User::find()
 *         ->select([
 *             'name',
 *             (new suql\core\Modifier('max'))->applyTo(['id' => 'max'])
 *         ]),
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Modifier
{
    /**
     * @var string название модификатора
     */
    private $modifier;
    /**
     * @var array параметры модификатора
     */
    private $params;
    /**
     * @var string|array поле (в двух возможных форматах) к которому применяется модификатор
     * 1: [<field> => <alias>]
     * 2: '<field>[@<alias>]'
     */
    private $field;
    /**
     * Constructor
     * @param string $modifier имя модификатора
     * @param array $params параметры модификатора
     */
    function __construct($modifier, $params = [])
    {
        $this->modifier = $modifier;
        $this->params = $params;
    }
    /**
     * Получить название модификатора
     * @return string
     */
    public function getModifier()
    {
        return $this->modifier;
    }
    /**
     * Получить параметры модификатора
     * @return array
     */
    public function getParams()
    {
        return $this->params;
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
     * Применить модификатор к полю
     * @param string|array $field
     * @return self
     */
    public function applyTo($field)
    {
        $this->field = $field;
        return $this;
    }
}
