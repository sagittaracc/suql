<?php

namespace test\suql\models;

use suql\syntax\ControllerInterface;
use suql\syntax\SuQL;

class Query20 extends SuQL implements ControllerInterface
{
    # [Route(name="route1", method="GET")]
    public function route1()
    {
        return ['foo' => 'bar'];
    }
}
