<?php

namespace suql\builder;

/**
 * Драйверы работы с SQL
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class SQLDriver
{
    /**
     * @var array перечень поддерживаемых СУБД
     */
    private $driverList = [
        'mysql' => 'MySQLBuilder',
    ];
    /**
     * @var string используемый драйвер
     */
    private $driver;
    /**
     * Constructor
     * @param string $driver
     */
    function __construct($driver)
    {
        $this->driver = $driver;
    }
    /**
     * Получить используемый драйвер СУБД
     * @return string используемый драйвер (mysql, postgresql etc.)
     */
    public function getDriver()
    {
        return $this->driver;
    }
    /**
     * Получить класс билдера
     * Возвращает класс билдера или null если запрошенная СУБД не поддерживается
     * @return SQLBuilder|null
     */
    public function getBuilder()
    {
        return isset($this->driverList[$this->driver])
            ? $this->driverList[$this->driver]
            : null;
    }
}
