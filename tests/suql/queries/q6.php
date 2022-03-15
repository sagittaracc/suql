<?php
return <<<SQL
    select
        *
    from `table_1`
    order by
        `table_1`.`f1` desc,
        `table_1`.`f2` asc
SQL;