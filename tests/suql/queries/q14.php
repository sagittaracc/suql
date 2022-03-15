<?php
return <<<SQL
    select
        `test_suql_models_Query7`.`f1`,
        `test_suql_models_Query7`.`f2`
    from (
        select
            `table_7`.`f1`,
            `table_7`.`f2`,
            `table_7`.`f3`
        from `table_7`
    ) test_suql_models_Query7
SQL;