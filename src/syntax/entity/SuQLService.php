<?php

namespace suql\syntax\entity;

use suql\syntax\ServiceInterface;

abstract class SuQLService extends SuQLArray implements ServiceInterface
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
     * @var mixed данные сервиса
     */
    private $data;
    /**
     * @inheritdoc
     */
    public function table()
    {
        return 'service_' . $this->query();
    }
    /**
     * Задание данные сервиса
     * @param mixed $data данные сервиса
     */
    public function setData($data)
    {
        $this->data = $data;
    }
    /**
     * @inheritdoc
     */
    public function data()
    {
        return $this->data;
    }
    /**
     * @inheritdoc
     */
    public static function find()
    {
        // Задание ссылки
        // Задание метода
        // Задание тела запроса
        // Получение данных по запросу
        // Установка данных
        // Вызов родительского parent::all();
    }
}
