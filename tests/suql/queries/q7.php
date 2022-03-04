<?php
return <<<SQL
    select
        table_1.f1,
        count(table_1.f1) as count
    from table_1
    group by table_1.f1
SQL;