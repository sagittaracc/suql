<?php

namespace test\suql\models;

use suql\syntax\ControllerInterface;
use suql\syntax\SuQL;

class Query20 extends SuQL implements ControllerInterface
{
    # Route(route="some/route", method="GET")
    public function routeHandler()
    {
        return ['foo' => 'bar'];
    }

    # Route(route="raw/sql", method="GET")
    public function rawsql()
    {
        return Query1::all()->getRawSql();
    }
}
