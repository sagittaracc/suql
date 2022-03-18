<?php

namespace suql\syntax;

/**
 * Описание модели
 * 1. Поля модели по типам и ограничениям
 * 2. ...
 * 
 * @author Yuriy Arutyunyan <sagittaracc@gmail.com>
 */
trait Model
{
    /**
     * @var array $columnList перечень полей модели
     */
    private $columnList = [];
    /**
     * @var string|null $currentColumn текущее поле при построении модели в билдере
     */
    private $currentColumn = null;
    /**
     * Объявление нового поля модели
     * @param string $column имя поля
     * @return self
     */
    public function column($column)
    {
        $this->columnList[$column] = Column::create($column);
        $this->currentColumn = $column;
        return $this;
    }
    /**
     * Получает перечень всех полей модели
     * @return array
     */
    public function getColumns()
    {
        return $this->columnList;
    }
    /**
     * Получает текущее конфигурируемое поле
     * @return string
     */
    public function getCurrentColumn()
    {
        return $this->currentColumn;
    }
    /**
     * Устанавливаем тип текущего поля
     * @param string $type
     * @return self
     */
    public function setType($type)
    {
        $this->columnList[$this->currentColumn]->setType($type);
        return $this;
    }
    /**
     * Устанавливает длину текущего поля
     * @param integer $length
     * @return self
     */
    public function setLength($length)
    {
        $this->columnList[$this->currentColumn]->setLength($length);
        return $this;
    }
    /**
     * Устанавливает значение текущего поля по умолчанию
     * @param mixed $default
     * @return self
     */
    public function setDefault($default)
    {
        $this->columnList[$this->currentColumn]->setDefault($default);
        return $this;
    }
    /**
     * Получает поле по его имени
     * @param string $column
     * @return suql\syntax\Column
     */
    public function getColumn($column)
    {
        return $this->columnList[$column];
    }
}