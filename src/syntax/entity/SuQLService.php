<?php

namespace suql\syntax\entity;

use GuzzleHttp\Client;
use suql\annotation\ServiceAnnotation;
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
     * @var string proxy
     */
    protected $proxy = null;
    /**
     * @inheritdoc
     */
    public static function find($body = [])
    {
        $instance = new static();

        $annotation = ServiceAnnotation::from($instance)->read();
        $instance->uri = $annotation->uri;
        $instance->method = $annotation->method;
        $instance->uri = $instance->method === 'GET' ? $instance->uri . '?' . http_build_query($body) : $instance->uri;
        $instance->body = $instance->method === 'POST' ? $body : [];

        $client = new Client(['proxy' => $instance->proxy, 'headers' => ['Content-Type' => 'application/json']]);
        $response = $client->request($instance->method, $instance->uri, $instance->body);
        $content = $response->getBody()->getContents();

        return $instance->processContent($content);
    }
    /**
     * Обработка полученного из сервиса контента
     * @param string $content
     * @return mixed
     */
    protected function processContent($content)
    {
        static::$data = json_decode($content, true);
        return parent::all();
    }
}
