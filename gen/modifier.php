<?php

echo 'Namespace [app\models]: ';
$namespace = trim(fgets(STDIN)) ?: 'app\modifier';

echo "Classname [SuQLClassModifier]: ";
$class = trim(fgets(STDIN)) ?: 'SuQLClassModifier';

echo "Modifier's name [name]: ";
$name = trim(fgets(STDIN)) ?: 'name';

$filedata = file_get_contents(__DIR__.'/tpl/modifier.php', 'r');

$filedata = str_replace(
    [
        '{namespace}',
        '{class}',
        '{name}',
    ],
    [
        !empty($namespace) ? "namespace $namespace;" : '',
        $class,
        $name,
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