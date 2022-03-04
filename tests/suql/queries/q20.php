<?php
return <<<SQL
    select
        *
    from (
        select
            *
        from table_10
    )
SQL;