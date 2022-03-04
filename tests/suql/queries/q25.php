<?php

return <<<SQL
    select
        table_1.f1 as af1,
        table_1.f2
    from table_1
    where table_1.f1 not in (
        select distinct
            table_2.f1
        from table_2
    )
SQL;