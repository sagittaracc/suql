<?php
return <<<SQL
    select
        *
    from `users`
    inner join `products` on `users`.`product_id` = `products`.`id`
    inner join `categories` on `products`.`category_id` = `categories`.`id`
SQL;