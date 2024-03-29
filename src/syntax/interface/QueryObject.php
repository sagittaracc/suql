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
     * Описание запроса
     * @return self
     */
    public function view();
    /**
     * Задание связей с другими моделями
     * @return array
     */
    public function relations();
}