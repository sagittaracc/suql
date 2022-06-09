<?php

namespace test\suql\models\buffers;

use test\suql\models\arrays\TestArray;
use test\suql\models\Query10;

class Buffer extends TestArray
{
    public function relations()
    {
        return [
            Query10::class => ['f1' => 'f1'],
        ];
    }
}