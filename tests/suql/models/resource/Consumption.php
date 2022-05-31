<?php

namespace test\suql\models\resource;

use suql\syntax\entity\SuQLTable;

# [Table(name="consumption")]
class Consumption extends SuQLTable
{
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public function relations()
    {
        return [
            'LastConsumptionTime' => [
                'counter' => 'counterId',
                'tarif' => 'tarif',
                'time' => 'time',
            ],
        ];
    }
}