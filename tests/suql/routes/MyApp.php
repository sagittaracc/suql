<?php

namespace test\suql\routes;

use suql\syntax\entity\RPCMethod;
use suql\syntax\entity\RPCRouter;

class MyApp extends RPCRouter
{
    #[RPCMethod(name: "foo")]
    public function index($a, $b)
    {
        var_dump($a, $b);
    }
}