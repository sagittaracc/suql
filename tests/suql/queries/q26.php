<?php

return <<<SQL
    select
        `table_1`.`f1`
    from `table_1`
    inner join table_2 on table_1.id = table_2.id
    inner join (
        select
            `table_15`.`*`
        from `table_15`
        limit 1
    ) query_15 on table_2.id = table_15.id
SQL;