<?php

namespace suql\syntax;

/**
 * Объект запроса
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
interface QueryObject
{
    /**
     * Запрос должен иметь имя запроса
     * @return string
     */
    public function query();
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
     * Реальная вьюха или программная
     * @return boolean
     */
    public function real();
    /**
     * Описание запроса
     * @return self
     */
    public function view();
    /**
     * Ссылка на объект PDO
     * @return \PDO
     */
    public function getDb();
}