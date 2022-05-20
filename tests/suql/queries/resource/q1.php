<?php
return <<<SQL
    select
        `consumption`.`counterId` as `counter`,
        `consumption`.`tarifId` as `tarif`,
        max(`consumption`.`time`) as `time`
    from `consumption`
    group by `consumption`.`counterId`, `consumption`.`tarifId`
SQL;
