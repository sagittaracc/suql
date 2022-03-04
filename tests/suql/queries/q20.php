<?php
return <<<SQL
    select
        *
    from (
        select
            *
        from table_1
    )
SQL;