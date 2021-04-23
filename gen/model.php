<?php

$options = getopt(null, [
    'namespace:',
    'class:',
    'type:',
    'table:',
]);

$file = file_get_contents(__DIR__.'/tpl/suql.php', 'r');

$file = str_replace(
    [
        '{namespace}',
        '{class}',
        '{utype}',
        '{type}',
        '{table}',
    ],
    [
        "namespace {$options['namespace']}",
        $options['class'],
        ucfirst($options['type']),
        $options['type'],
        $options['type'] === 'table' ? "'".$options['table']."'" : 'Model::find()',
    ],
    $file
);

echo $file;