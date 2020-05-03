# Sugar SQL

### What is this?
SuQL is syntactic sugar for SQL.

### Why do you need this?
1. Make developing process faster.
2. Make queries easy to read and write.
3. Expand SuQL syntax on your own.

### How do you use this?
There are two approaches:
1. [Simple Sugar SQL.](#simple-sugar-sql)
2. [Object Oriented Sugar SQL.](#object-oriented-sugar-sql)

#### Simple Sugar SQL
```php
// Setting up tables relations
$db = (new SuQL())->rel(['users' => 'a'], ['user_group' => 'b'], 'a.id = b.user_id')
                  ->rel(['user_group' => 'a'], ['groups' => 'b'], 'a.group_id = b.id');

$db->query("
  -- Getting how many users of each group
  @allUsers = SELECT FROM users
              INNER JOIN user_group
              INNER JOIN groups
                name@gname
                name.group.count@cnt
              ;

  -- How many admins?
  SELECT FROM @allUsers
    gname,
    cnt
  WHERE gname = 'admin'
  ;
")
```

#### Object Oriented Sugar SQL
```php
// Setting up tables relations
$db = (new OSuQL)->rel(['users' => 'u'], ['user_group' => 'ug'], 'u.id = ug.user_id')
                 ->rel(['user_group' => 'ug'], ['groups' => 'g'], 'ug.group_id = g.id');

// Getting how many users of each group
$db->query('usersCountOfEachGroup')
    ->users()
    ->user_group()
    ->groups()
      ->field(['name' => 'g_name'])
      ->field(['name' => 'count'])->group()->count();

// How many admins?
$db->query()
    ->usersCountOfEachGroup()
      ->field('g_name')
      ->field('count')
    ->where("g_name = 'admin'");
```

# Documentation

### Sample Database

![Sugar SQL Sample Database](/assets/images/Sugar-SQL-Sample-Database.png)



# SuQL Syntax
## Querying Data

**Sugar SQL approach:**
```sql
SELECT FROM users
  id,
  name
;
```

**Object Oriented approach**
```php
$db = (new OSuQL)->query()
                  ->users()
                    ->field('id')
                    ->field('name');
```
|id   |name   |
|---|---|
|1   |Yuriy   |
|2   |Alex   |
|3   |Vlad   |
|4   |Den   |

**Sugar SQL approach**
```sql
SELECT FROM users
  *
;
```

**Object Oriented approach**
```php
$db = (new OSuQL)->query()
                  ->users()
                    ->field('*');
```

**Sugar SQL approach**
```sql
SELECT FROM users
  id@u_id,
  name@u_name
;
```

**Object Oriented approach**
```php
$db = (new OSuQL)->query()
                  ->users()
                    ->field(['id' => 'u_id'])
                    ->field(['name' => 'u_name']);
```
|u_id   |u_name   |
|---|---|
|1   |Yuriy   |
|2   |Alex   |
|3   |Vlad   |
|4   |Den   |



## Filtering Data
> Example: Get all the users with even id's

**Sugar SQL approach**
```sql
SELECT FROM users
  id@u_id,
  name@u_name
WHERE u_id % 2 = 0
;
```

**Object Oriented approach**
```php
$db = (new OSuQL)->query()
                  ->users()
                    ->field(['id' => 'u_id'])
                    ->field(['name' => 'u_name'])
                  ->where('u_id % 2 = 0');
```
|u_id   |u_name   |
|---|---|
|2   |Alex   |
|4   |Den   |

> Example: Get all the users who do not belong to any groups

**Sugar SQL approach**
```sql
@users_belong_to_any_group = SELECT DISTINCT FROM user_group
                              user_id
                             ;
SELECT FROM users
  id@uid,
  name
WHERE uid not in @users_belong_to_any_group
;
```

**Object Oriented approach**
```php
$db = (new OSuQL)->query('users_belong_to_any_group')
                  ->user_group('distinct')
                    ->field('user_id');
$db->query()
    ->users()
      ->field('name')
    ->where('users.id not in #users_belong_to_any_group');
```

> Example: Get the first two users

**Sugar SQL approach**
```sql
SELECT FROM users
  *
LIMIT 0, 2
;
```

**Object Oriented approach**
```php
$db = (new OSuQL)->query()
                  ->users()
                    ->field('*')
                  ->offset(0)
                  ->limit(2);
```
| id | name  | registration        |
|----|-------|---------------------|
| 1  | Yuriy | 2019-12-10 10:03:16 |
| 2  | Alex  | 2020-04-08 10:03:16 |

> Example: Get uniques users names

```sql
SELECT DISTINCT FROM users
  name
;
```


## Joining Multiple Tables
> Example: Link all three tables together to see how many admins we have.

**Sugar SQL approach**
```sql
SELECT FROM users
INNER JOIN user_group
INNER JOIN groups
  id@g_id,
  name@g_name
;
```

**Object Oriented approach**
```php
$db = (new OSuQL)->rel(['users' => 'u'], ['user_group' => 'ug'], 'u.id = ug.user_id')
                 ->rel(['user_group' => 'ug'], ['groups' => 'g'], 'ug.group_id = g.id');

$db->query()
    ->users()
    ->user_group()
    ->groups()
      ->field(['name' => 'g_name'])
```
|g_name   |
|---|
|admin   |
|admin   |
|admin   |
|user   |



## Grouping Data
> Example: How many admins? Use the count modifier to calc the exact number.

**Sugar SQL approach**
```sql
SELECT FROM users
INNER JOIN user_group
INNER JOIN groups
  name@g_name,
  name.group.count@count
WHERE g_name = 'admin'
;
```

**Object Oriented approach**
```php
$db = (new OSuQL)->rel(['users' => 'u'], ['user_group' => 'ug'], 'u.id = ug.user_id')
                 ->rel(['user_group' => 'ug'], ['groups' => 'g'], 'ug.group_id = g.id');

$db->query()
    ->users()
    ->user_group()
    ->groups()
      ->field(['name' => 'g_name'])
      ->field(['name' => 'count'])->group()->count()
    ->where("g_name = 'admin'");
```
|g_name   |count   |
|---|---|
|admin   |3   |



## Nested Queries

**Sugar SQL approach**
```sql
@allGroupsCount = SELECT FROM users
                  INNER JOIN user_group
                  INNER JOIN groups
                    name@g_name,
                    name.group.count@count
                  ;
SELECT FROM allGroupsCount
  g_name,
  count
WHERE g_name = 'admin'
;
```

**Object Oriented approach**
```php
$db = (new OSuQL)->rel(['users' => 'u'], ['user_group' => 'ug'], 'u.id = ug.user_id')
                 ->rel(['user_group' => 'ug'], ['groups' => 'g'], 'ug.group_id = g.id');

$db->query('allGroupsCount')
    ->users()
    ->user_group()
    ->groups()
      ->field(['name' => 'g_name'])
      ->field(['name' => 'count'])->group()->count();

$db->query()
    ->allGroupsCount()
      ->field('g_name')
      ->field('count')
    ->where("g_name = 'admin'");
```
|g_name   |count   |
|---|---|
|admin   |3   |



## Sorting Data

**Sugar SQL approach**
```sql
SELECT FROM users
INNER JOIN user_group
INNER JOIN groups
  name@gname,
  name.group.count.asc@count
;
```

**Object Oriented approach**
```php
$db = (new OSuQL)->rel(['users' => 'u'], ['user_group' => 'ug'], 'u.id = ug.user_id')
                 ->rel(['user_group' => 'ug'], ['groups' => 'g'], 'ug.group_id = g.id');

$db->query()
    ->users()
    ->user_group()
    ->groups()
      ->field(['name' => 'gname'])
      ->field(['name' => 'count'])->group()->count()->asc();
```
| user_id | id | gname | count |
|---------|----|-------|-------|
| 4       | 2  | user  | 1     |
| 1       | 1  | admin | 3     |



## CASE Expression
You can create SQL CASE Expressions by using custom modifiers.



## Modifiers
To develop your own modifiers:
1. Include `dist/suql.phar` in your project
2. Define the `SQLModifier` class that has to be extended from the `SQLBaseModifier` class.
3. Define a public static function with the `mod_` prefix and then the name of the modifier.
> Example: Define a standart SQL function `min`
```php
class SQLModifier extends SQLBaseModifier
{
  public static function mod_min(&$queryObject, $field) {
    parent::default_handler('min', $queryObject, $field);
  }
}
```
> Example: When has the first user registered?

```sql
SELECT FROM users
  registration.min@firstReg
;
```
| firstReg            |
|---------------------|
| 2019-06-12 10:03:16 |



### CASE Expression as a custom modifier
> Example: Show groups permissions depends on the group name.
```php
class SQLModifier extends SQLBaseModifier
{
  // ...
  public static function mod_permission(&$queryObject, $field) {
    parent::mod_case([
      "$ = 'admin'" => "'can do everything'",
      "$ = 'user'"  => "'can read only'",
      'default'     => "'can do nothing'",
    ], $queryObject, $field);
  }
  // ...
}
```

**Sugar SQL approach**
```sql
SELECT FROM groups
  id,
  name,
  name.permission@permission
;
```

**Object Oriented approach**
```php
$db = (new OSuQL)->query()
                  ->groups()
                    ->field('id')
                    ->field('name')
                    ->field(['name' => 'permission'])->permission();
```
| id | name  | permission        |
|----|-------|-------------------|
| 1  | admin | can do everything |
| 2  | user  | can read only     |
| 3  | guest | can do nothing    |



## Conclusion

SuQL is all about modifiers. They already replace standart SQL clauses such as `WHERE`, `GROUP`, `JOIN`, `ORDER` etc and SQL functions.

More than that, you can develop your own modifiers.
