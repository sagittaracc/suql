<?php

namespace suql\syntax\entity;

use GuzzleHttp\Client;
use suql\syntax\ServiceInterface;

abstract class SuQLService extends SuQLArray implements ServiceInterface
{
    /**
     * Конструктор
     */
    public function __construct()
    {
    }
    /**
     * @var string ссылка сервиса
     */
    private $uri;
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
        $instance = new static();

        $instance->uri = '';
        $instance->method = 'POST';
        $instance->body = [];

        $client = new Client();
        // $response = $client->request($instance->method, $instance->uri, $instance->body);
        // $data = $response->getBody();
        $data = [
            ['user_id' => 1, 'login' => 'login1'],
            ['user_id' => 2, 'login' => 'login2'],
        ];

        static::$data = $data;

        return parent::all();
    }
}
