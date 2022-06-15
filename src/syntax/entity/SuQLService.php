<?php

namespace suql\syntax\entity;

use GuzzleHttp\Client;
use suql\annotation\ProxyAnnotation;
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

        $serviceAnnotation = ServiceAnnotation::from($instance)->read();
        $proxyAnnotation = ProxyAnnotation::from($instance)->read();
        $instance->uri = $serviceAnnotation->uri;
        $instance->method = $serviceAnnotation->method;
        $instance->uri = $instance->method === 'GET' ? $instance->uri . '?' . http_build_query($body) : $instance->uri;
        $instance->body = $instance->method === 'POST' ? $body : [];
        if (!is_null($proxyAnnotation->url)) {
            $url = $proxyAnnotation->url;
            $port = $proxyAnnotation->port;
            if (!is_null($proxyAnnotation->user)) {
                $proxyAuth = "$proxyAnnotation->user:$proxyAnnotation->pass";
                $url = str_replace('://', '://' . $proxyAuth . '@', $url);
            }
            $instance->proxy = "$url:$port";
        }

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
