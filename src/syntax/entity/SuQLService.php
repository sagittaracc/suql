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
        // Возвращение экземпляра SuQL Array
        return (new class extends SuQLArray
        {
            protected static $builderClass = 'suql\\builder\\MySQLBuilder';

            public function query()
            {
                return 'temp_query';
            }

            public function data()
            {
                /**
                 * Допустим храню пароли в массиве а не в базе данных
                 */
                return [
                    ['user_id' => 1, 'login' => 'login1'],
                    ['user_id' => 2, 'login' => 'login2'],
                ];
            }

            public function getDb()
            {
                return Container::get('db_test');
            }
        })::all();
    }
}
