<?php

return [
    'main' => function ($query, $field) {
        $query->where("$field = 0");
    },
];