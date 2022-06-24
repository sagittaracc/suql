<?php

return [
    'param1' => function ($query) {
        $query->select(['f1', 'f2']);
    },
];