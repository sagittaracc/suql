<?php

use app\models\User;
use suql\db\Container;

require '../vendor/autoload.php';

// Connect to the database
Container::create(require __DIR__ . '/config/db.php');

// Fetch data from the database
$userList = User::all()->fetchAll();

print_r($userList);