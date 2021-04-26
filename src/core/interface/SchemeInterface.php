<?php

namespace suql\core;

/**
 * Интерфейс реализации работы со схемой базы данных
 * Должен реализовать метод хранения постоянных и временных связей
 * между таблицами и вьюхами
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
interface SchemeInterface
{
    /**
     * Создание постоянной связи
     * @param string $leftTable первая таблица/вьюха
     * @param string $rightTable вторая таблица/вьюха
     * @param string $on условие связи
     * @param boolean $temporary по умолчанию связь постоянная
     */
    public function rel($leftTable, $rightTable, $on, $temporary = false);
    /**
     * Создание временной связи
     * @param string $leftTable первая таблица/вьюха
     * @param string $rightTable вторая таблица/вьюха
     * @param string $on условие связи
     */
    public function temp_rel($leftTable, $rightTable, $on);
}