<?php
return [

    'query' => <<<SQL
        select
            table_1.f1
        from table_1
        where table_1.f1 % 2 = 0 and table_1.f1 > :ph0_385e2713c49b536f4b5b2f120cac9c4a
SQL,

    'params' => [
        ':ph0_385e2713c49b536f4b5b2f120cac9c4a' => 1
    ],
];