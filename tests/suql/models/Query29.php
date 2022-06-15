<?php

namespace test\suql\models;

use suql\syntax\entity\SuQLJsonService;
use test\suql\connections\TestMySQLConnection;

# [Request(uri="https://api.random.org/json-rpc/2/invoke", method="POST")]
class Query29 extends SuQLJsonService
{
    use TestMySQLConnection;

    protected function processContent($content)
    {
        $content = json_decode($content, true);
        $data = $content['result']['random']['data'];
        static::$data = [
            ['f1' => $data[0]],
            ['f1' => $data[1]],
            ['f1' => $data[2]],
        ];
        return parent::all();
    }

    public function relations()
    {
        return [
            Query26::class => ['f1' => 'f1'],
        ];
    }
}
