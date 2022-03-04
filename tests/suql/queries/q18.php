<?php
return [

    'query' => <<<SQL
        select
            *
        from table_1
        where
            f1 > :ph0_8008c0fb0d9e45eeab00d02d4dc6bf1b
        and f2 < :ph0_52b577f9a00337a21f2f63d83847c058
SQL,

    'params' => [
        ':ph0_8008c0fb0d9e45eeab00d02d4dc6bf1b' => 1,
        ':ph0_52b577f9a00337a21f2f63d83847c058' => 2,
    ],
];