<?php

namespace suql\core;

/**
 * Обработка имени с заданием алиаса в трех возможных форматах:
 *   Separated new Name(<name>, <alias>)
 *   Array     new Name([<name> => <alias>])
 *   String    new Name('<name>@<alias>')
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Name
{
    /**
     * @var string имя
     */
    public $name = '';
    /**
     * @var string алиас
     */
    public $alias = '';
    /**
     * Constructor
     */
    function __construct()
    {
        if (func_num_args() === 2) {
            $this->parseSeparated(func_get_args());
        } else if (func_num_args() === 1) {
            $arg = func_get_arg(0);

            if (is_array($arg)) {
                $this->parseArray($arg);
            } else if (is_string($arg)) {
                $this->parseString($arg);
            }
        }
    }
    /**
     * Разбор параметров в Separated формате
     */
    private function parseSeparated($args)
    {
        $this->name = $args[0];
        $this->alias = $args[1];
    }
    /**
     * Разбор параметров в Array формате
     */
    private function parseArray($array)
    {
        foreach ($array as $name => $alias) break;
        $this->name = $name;
        $this->alias = $alias;
    }
    /**
     * Разбор параметров в String формате
     */
    private function parseString($string)
    {
        $parts = explode('@', $string);
        $this->name = isset($parts[0]) ? $parts[0] : '';
        $this->alias = isset($parts[1]) ? $parts[1] : '';
    }
    /**
     * Выводит имя в заданном формате
     * @param string $s формат вывода. Поддерживает два плейсхолдера %n - имя, %a - алиас
     * @return string
     */
    public function format($s)
    {
        return $this->alias
            ? str_replace(['%n', '%a'], [$this->name, $this->alias], $s)
            : $this->name;
    }
}
