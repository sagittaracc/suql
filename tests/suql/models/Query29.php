<?php

namespace test\suql\models;

use suql\syntax\entity\SuQLJsonService;

# [Request(uri="https://api.random.org/json-rpc/2/invoke", method="POST")]
class Query29 extends SuQLJsonService
{
    protected function processContent($content)
    {
        $content = json_decode($content, true);
        return $content['result']['random']['data'];
    }
}
