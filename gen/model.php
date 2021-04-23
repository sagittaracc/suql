<?php

echo 'Namespace [app\models]: ';
$namespace = trim(fgets(STDIN)) ?: 'app\models';

echo 'What is it, table or view? [table]: ';
$type = trim(fgets(STDIN)) ?: 'table';
$postfix = $type === 'view' ? 'View' : '';

echo "Classname [Model{$postfix}]: ";
$class = trim(fgets(STDIN)) ?: 'Model' . $postfix;

echo "Table: ";
$table = trim(fgets(STDIN));

$filedata = file_get_contents(__DIR__.'/tpl/model.php', 'r');

$filedata = str_replace(
    [
        '{namespace}',
        '{class}',
        '{utype}',
        '{type}',
        '{table}',
    ],
    [
        !empty($namespace) ? "namespace $namespace;" : '',
        $class,
        ucfirst($type),
        $type,
        $type === 'table' ? "'".$table."'" : $table.'::find()',
    ],
    $filedata
);

echo $filedata . "\n";

echo 'Is everything okay [y/n]? ';
$okay = trim(fgets(STDIN)) ?: 'y';

if ($okay === 'y')
{
    echo 'Save as: ';
    $filename = trim(fgets(STDIN));

    if ($filename)
        file_put_contents($filename, $filedata);
}