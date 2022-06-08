<?php

namespace suql\syntax\entity;

use suql\db\Container;
use suql\syntax\ServiceInterface;

abstract class SuQLService implements ServiceInterface
{
    /**
     * @var string ссылка сервиса
     */
    public $href;
    /**
     * @var string метод сервиса
     */
    public $method;
    /**
     * @var array тело запроса
     */
    public $body;
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
        $instance = new class extends SuQLArray
        {
            protected static $builderClass = 'suql\\builder\\MySQLBuilder';

            public function query()
            {
                return 'temp_query';
            }

            public function getDb()
            {
                return Container::get('db_test');
            }
        };

        return $instance->all($data);
    }
}
