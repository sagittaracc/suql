<?php
return <<<SQL
    select
        `consumption`.`counterId` as `counter`,
        `consumption`.`tarifId` as `tarif`,
        `consumption`.`consumption`
    from `consumption`
    inner join (
        select
            `consumption`.`counterId` as `counter`,
            `consumption`.`tarifId` as `tarif`,
            max(`consumption`.`time`) as `time`
        from `consumption`
        group by `consumption`.`counterId`, `consumption`.`tarifId`
    ) LastConsumptionTime
    on `consumption`.`counterId` = `LastConsumptionTime`.`counter`
    and `consumption`.`tarif` = `LastConsumptionTime`.`tarif`
    and `consumption`.`time` = `LastConsumptionTime`.`time`
SQL;
