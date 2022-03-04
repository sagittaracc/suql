<?php
return <<<SQL
    select
        table_1.*,
        'Yuriy' as author
    from table_1
SQL;