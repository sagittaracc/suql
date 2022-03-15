<?php
return <<<SQL
    select
        `table_1`.`f1`,
        `table_1`.`f2`
    from `table_1`
    where `table_1`.`f1` >= DATE_ADD(CURDATE(), INTERVAL -3 day)
SQL;