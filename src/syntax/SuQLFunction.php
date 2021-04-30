<?php

namespace suql\syntax;

/**
 * Класс обработчик хранимых функций
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class SuQLFunction extends RawSuQL implements SuQLFunctionInterface
{
    /**
     * Создание экземляра класса хранимой функции
     * @param string $name имя функции
     * @return self
     */
    public static function find($name = null)
    {
        $function = parent::func($name);
        return $function;
    }
    /**
     * Задать параметры для хранимой функции
     * @return self
     */
    public function params()
    {
        $this->getQuery($this->query())->addParams(func_get_args());
        return $this;
    }
}
