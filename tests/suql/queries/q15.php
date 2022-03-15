<?php
return <<<SQL
    (select `table_1`.`f1`, `table_1`.`f2`, `table_1`.`f3` from `table_1`)
        union
    (select `table_2`.`f1`, `table_2`.`f2`, `table_2`.`f3` from `table_2`)
        union
    (select `table_3`.`f1`, `table_3`.`f2`, `table_3`.`f3` from `table_3`)
SQL;