<?php
return <<<SQL
    select
        distinct `table_1`.`f1`, `table_1`.`f2`
    from `table_1`
SQL;