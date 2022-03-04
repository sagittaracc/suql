<?php
return <<<SQL
    select
        test_suql_models_Query7.field_1,
        test_suql_models_Query7.field_2
    from (
        select
            table_7.field_1,
            table_7.field_2,
            table_7.field_3
        from table_7
    ) test_suql_models_Query7
SQL;