<?php

namespace suql\syntax\entity;

abstract class SuQLJsonService extends SuQLService
{
    /**
     * @var string вызываемый rpc метод
     */
    private $rpcMethod;
    /**
     * Создает экземляр rpc метода
     * @param string $method
     * @return static
     */
    public static function call($method)
    {
        $instance = new static();
        $instance->rpcMethod = $method;
        return $instance;
    }
    /**
     * Задает параметры rpc метода
     * @param array $params
     * @return \suql\syntax\entity\SuQLService
     */
    public function withParams($params)
    {
        $body = [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => $this->rpcMethod,
                'params' => $params,
                'id' => uniqid(),
            ],
        ];

        return parent::find($body);
    }
    /**
     * @inheritdoc
     */
    protected function processContent($content)
    {
        return json_decode($content, true);
    }
}