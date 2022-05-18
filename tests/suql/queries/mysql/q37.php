<?php
return <<<SQL
    select
        `table_1`.`f1`
    from `table_1`
    inner join (
        select
            max(`table_13`.`f1`) as `mf1`
        from `table_13`
    ) query_13 on `table_1`.`id` = `table_13`.`id`
SQL;