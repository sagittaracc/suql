<?php

namespace suql\syntax;

/**
 * Объект обращающийся к базе данных
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
interface TableInterface
{
    /**
     * Запрос должен ссылаться на таблицу
     * @return string
     */
    public function table();
    /**
     * Создание модели
     * @return array
     */
    public function create();
    /**
     * Описание запроса
     * @return self
     */
    public function view();
    /**
     * Задание связей с другими моделями
     * @return array
     */
    public function relations();
    /**
     * Ссылка на объект PDO
     * @return \suql\db\pdo\Connection
     */
    public function getDb();
}