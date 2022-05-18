<?php
return <<<SQL
    select
        `table_1`.`f1` as `af1`,
        `table_3`.`f2` as `af2`,
        count(`table_3`.`f3`) as `af3`
    from `table_1`
    inner join `table_2` on `table_1`.`id` = `table_2`.`id`
    inner join `table_3` on `table_2`.`id` = `table_3`.`id`
    group by `table_3`.`f3`
SQL;