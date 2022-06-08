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
    protected $uri;
    /**
     * @var string метод сервиса
     */
    protected $method;
    /**
     * @var array тело запроса
     */
    protected $body;
    /**
     * @inheritdoc
     */
    public static function find($body = [])
    {
        $instance = new static();

        // $instance->uri = read from annotation
        // $instance->method = read from annotation
        $instance->uri = $instance->method === 'GET' ? $instance->uri . '?' . http_build_query($body) : $instance->uri;
        $instance->body = $instance->method === 'POST' ? $body : [];

        $client = new Client();
        $response = $client->request($instance->method, $instance->uri, $instance->body);
        $content = $response->getBody()->getContents();

        static::$data = json_decode($content, true);

        return parent::all();
    }
}
