<?php
return <<<SQL
    select
        `table_1`.`f1`,
        `t3`.`f1` as `af1`,
        `t3`.`f2` as `af2`
    from `table_1`
    inner join `table_2` on `table_1`.`id` = `table_2`.`id`
    inner join `table_3` t3 on `table_2`.`id` = `t3`.`id`
SQL;