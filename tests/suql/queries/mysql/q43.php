<?php
return [

    'query' => <<<SQL
        select
            *
        from `table_1`
        where
            `table_1`.`f1` > 1 and
            (
                `table_1`.`f2` > 2 or
                `table_1`.`f2` < 3
            )
SQL,

    'params' => [
        ':ph0_8008c0fb0d9e45eeab00d02d4dc6bf1b' => 1,
        ':ph0_52b577f9a00337a21f2f63d83847c058' => 2,
        ':ph0_df41dd88e94a1d6f56fb80165480688b' => 3,
    ],
];