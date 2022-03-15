<?php
return [

    'query' => <<<SQL
        select
            `table_1`.`f1`
        from `table_1`
        where table_1.f1 % 2 = 0 and `table_1`.`f1` > :ph0_80c4c118433fb7fd9f6a4f313cb1e2ca
SQL,

    'params' => [
        ':ph0_80c4c118433fb7fd9f6a4f313cb1e2ca' => 1
    ],
];