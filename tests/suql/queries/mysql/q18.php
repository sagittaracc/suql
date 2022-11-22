<?php
return [

    'query' => <<<SQL
        select
            *
        from `table_1`
        where
            f2 < :ph0_52b577f9a00337a21f2f63d83847c058
        and `table_1`.`f1` > 1
SQL,

    'params' => [
        ':ph0_8008c0fb0d9e45eeab00d02d4dc6bf1b' => 1,
        ':ph0_52b577f9a00337a21f2f63d83847c058' => 2,
    ],
];