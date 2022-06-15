<?php

namespace suql\syntax\entity;

abstract class SuQLJsonService extends SuQLService
{
    public static function call($method, $params = [])
    {
        $body = [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => $method,
                'params' => $params,
                'id' => uniqid(),
            ],
        ];

        return parent::find($body);
    }

    protected function processContent($content)
    {
        return json_decode($content, true);
    }
}