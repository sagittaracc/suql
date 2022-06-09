<?php

return [
    'db_test' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'dbname' => 'db_test',
        'user' => 'root',
        'pass' => '',
    ],
    'db-sqlite' => [
        'driver' => 'sqlite',
        'file' => __DIR__ . '/../db/sqlite.db',
    ],
];
