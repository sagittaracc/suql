<?php

namespace suql\syntax\entity;

use suql\syntax\ServiceInterface;

abstract class SuQLService extends SuQLArray implements ServiceInterface
{
    /**
     * @var string ссылка сервиса
     */
    private $href;
    /**
     * @var string метод сервиса
     */
    private $method;
    /**
     * @var array тело запроса
     */
    private $body;
    /**
     * @inheritdoc
     */
    public static function find()
    {
        // Задание ссылки
        // Задание метода
        // Задание тела запроса
        // Получение данных по запросу
        $data = [
            ['user_id' => 1, 'login' => 'login1'],
            ['user_id' => 2, 'login' => 'login2'],
        ];
        // Возвращение экземпляра SuQL Array
        static::$data = $data;
        return parent::all();
    }
}
