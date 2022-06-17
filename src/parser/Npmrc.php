<?php

namespace parser;

/**
 * Класс для разбора конфига npmrc
 * 
 * @author Yuriy Arutyunyan <sagittaracc@gmail.com>
 */
class Npmrc
{
    /**
     * @var string файл конфига
     */
    private $file = null;
    /**
     * Contructor
     */
    function __construct($file)
    {
        if (file_exists($file)) {
            $this->file = $file;
        }
    }
    /**
     * Разбор настроек прокси из конфига
     * @return array
     */
    public function getProxy()
    {
        if (is_null($this->file)) {
            return [];
        }

        $regex = '/(?<protocol>https?):\/\/(?<user>\w+):(?<pass>\w+)@(?<host>[\w.]+):(?<port>\d+)/msi';
        $file = file_get_contents($this->file);
        preg_match($regex, $file, $matches);
        return $matches;
    }
}