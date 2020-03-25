<?php
include 'autoload.php';

$test_index = 1;

function test($suql, $sql) {
  global $test_index;
  echo "Test #$test_index - ";

  $a = str_replace(["\r\n", "\t", ' '], '', (new SuQL($suql))->pureSQL());
  $b = str_replace(["\r\n", "\t", ' '], '', $sql);
  // echo $a; echo "\n"; echo $b;
  $result = ($a === $b);
  if ($result) echo "OK\n";
  else echo "Fail\n";

  $test_index++;
}

test("users {*};", "select users.* from users");
test(
  "users {id@uid, name@uname} ~ uname = 'admin';",
  "
    select
      users.id as uid,
      users.name as uname
    from users
    having uname = 'admin'
  "
);
