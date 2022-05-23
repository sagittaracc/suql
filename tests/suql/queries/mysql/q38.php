<?php
return <<<SQL
    select
        `Query4`.`f1`,
        `Query4`.`f2`
    from (
        select
            `table_7`.`f1`,
            `table_7`.`f2`,
            `table_7`.`f3`
        from `table_7`
    ) Query4
SQL;