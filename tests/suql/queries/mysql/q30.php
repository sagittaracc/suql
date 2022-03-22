<?php
return <<<SQL
    select
        *
    from `table_20`
    inner join `table_21` on table_20.table21_id = table_21.id
    inner join `table_22` on table_21.table22_id = table_22.id
SQL;