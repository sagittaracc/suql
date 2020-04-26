# Sugar SQL
### What is this?
SuQL is syntactic sugar for SQL.
### Why do you need this?
1. Write queries easier and faster
2. Read queries in one breath
3. Make your own SuQL functions. SQL is your only limit.
# Documentation
## Sample Database
### users
|id   |name   |registration   |
|---|---|---|
|1   |Yuriy   |2019-12-10 10:03:16   |
|2   |Alex   |2020-04-08 10:03:16   |
|3   |Vlad   |2020-04-14 10:03:16   |
|4   |Den   |2019-06-12 10:03:16   |
### groups
|id   |name   |
|---|---|
|1   |admin   |
|2   |user   |
|3   |guest   |
### user_group
|id   |user_id   |group_id   |
|---|---|---|
|1   |1   |1   |
|2   |2   |1   |
|4   |3   |1   |
|5   |4   |2   |



# SuQL Syntax
## Querying Data
Specify a list of comma-separated columns you want to query the data from.

**Sugar SQL approach:**
```
users {
  id,
  name
};
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

Use the asterisk to query data from all columns of a table.

**Sugar SQL approach**
```
users {*};
```

**Object Oriented approach**
```php
$db = new (OSuQL)->query()
                  ->users()
                    ->field('*');
```

Use aliases to give a column a temporary name.

**Sugar SQL approach**
<pre>
users {
  id@<b>u_id</b>,
  name@<b>u_name</b>
};
</pre>

**Object Oriented approach**
```php
$db = new (OSuQL)->query()
                  ->users()
                    ->field('id', 'u_id')
                    ->field('name', 'u_name');
```
|u_id   |u_name   |
|---|---|
|1   |Yuriy   |
|2   |Alex   |
|3   |Vlad   |
|4   |Den   |



## Filtering Data
To select certain rows from a table, put a condition in curly brackets right after the querying data clause.
The condition syntax is the same as in the SQL WHERE clause.
> Example: Get all the users with even id's

**Sugar SQL approach**
<pre>
users {
  id@u_id,
  name@u_name
} <b>~ {u_id % 2 = 0}</b>;
</pre>

**Object Oriented approach**
```php
$db = new (OSuQL)->query()
                  ->users()
                    ->field('id', 'u_id')
                    ->field('name', 'u_name')
                  ->where('u_id % 2 = 0');
```
|u_id   |u_name   |
|---|---|
|2   |Alex   |
|4   |Den   |

You also can use nested queries this clause.
> Example: Get all the users who do not belong to any groups

**Sugar SQL approach**
<pre>
<b>#users_belong_to_any_group</b> = user_group.distinct {user_id};

users {
  name
} ~ {users.id not in <b>#users_belong_to_any_group</b>};
</pre>

**Object Oriented approach**
```php
$db = new (OSuQL)->query('users_belong_to_any_group')
                  ->user_group('distinct')
                    ->field('user_id');
$db->query()
    ->users()
      ->field('name')
    ->where('users.id not in #users_belong_to_any_group');
```

To retrieve a portion of rows, put `offset` and `limit` in square brackets right after the querying data clause.
> Example: Get the first two users

**Sugar SQL approach**
<pre>
users {*} <b>[0, 2]</b>;
</pre>

**Object Oriented approach**
```php
$db = new (OSuQL)->query()
                  ->users()
                    ->field('*')
                  ->offset(0)
                  ->limit(2);
```
| id | name  | registration        |
|----|-------|---------------------|
| 1  | Yuriy | 2019-12-10 10:03:16 |
| 2  | Alex  | 2020-04-08 10:03:16 |

To remove duplicates from a result set, you use the distinct modifier as follows:
> Example: Get uniques users names
<pre>
users.<b>distinct</b> {
  name
};
</pre>


## Joining Multiple Tables
To join multiple tables together, you use the `join` modifier. Link two tables by a relationship between two columns. One of them you apply the `join` modifier to and another you pass as a parameter.
> Example: Link all three tables together to see how many admins we have.

**Sugar SQL approach**
<pre>
users {
  id@u_id
}

user_group {
  user_id.<b>join</b>(u_id)
}

groups {
  id@g_id.<b>join</b>(user_group.group_id),
  name@g_name
};
</pre>

**Object Oriented approach**
```php
$db = new OSuQL()->rel(['users' => 'u'], ['user_group' => 'ug'], 'u.id = ug.user_id')
                 ->rel(['user_group' => 'ug'], ['groups' => 'g'], 'ug.group_id = g.id');

$db->query()
    ->users()
    ->user_group()
    ->groups()
      ->field('name', 'g_name')
```
|g_name   |
|---|
|admin   |
|admin   |
|admin   |
|user   |



## Grouping Data
To group rows into groups, you use the `group` modifier.
> Example: How many admins? Use the count modifier to calc the exact number.

**Sugar SQL approach**
<pre>
users {
  id@u_id
}

user_group {
  user_id.join(u_id)
}

groups {
  id@g_id.join(user_group.group_id),
  name@g_name,
  name@count.<b>group</b>.count
} ~ {g_name = 'admin'};
</pre>

**Object Oriented approach**
```php
$db = new OSuQL()->rel(['users' => 'u'], ['user_group' => 'ug'], 'u.id = ug.user_id')
                 ->rel(['user_group' => 'ug'], ['groups' => 'g'], 'ug.group_id = g.id');

$db->query()
    ->users()
    ->user_group()
    ->groups()
      ->field('name', 'g_name')
      ->field('name', 'count')->group()->count();
```
|g_name   |count   |
|---|---|
|admin   |3   |



## Nested Queries
Use variables for nested queries. A variable should start with the `#`

**Sugar SQL approach**
<pre>
<b>#allGroupsCount</b> = users {
  id@u_id
}

user_group {
  user_id.join(u_id)
}

groups {
  id@g_id.join(user_group.group_id),
  name@g_name,
  name@count.group.count
};

<b>allGroupsCount</b> {
  g_name,
  count
} ~ {g_name = 'admin'};
</pre>

**Object Oriented approach**
```php
$db = new OSuQL()->rel(['users' => 'u'], ['user_group' => 'ug'], 'u.id = ug.user_id')
                 ->rel(['user_group' => 'ug'], ['groups' => 'g'], 'ug.group_id = g.id');

$db->query('allGroupsCount')
    ->users()
    ->user_group()
    ->groups()
      ->field('name', 'g_name')
      ->field('name', 'count')->group()->count();

$db->query()
    allGroupsCount()
      ->field('g_name')
      ->field('count');
    ->where("g_name = 'admin'");
```
|g_name   |count   |
|---|---|
|admin   |3   |



## Sorting Data
Apply the sort modifier to the field you want to sort by. `asc` for ascending, `desc` for descending.
<pre>
users {}

user_group {
  user_id.join(users.id)
}

groups {
  id.join(user_group.group_id),
  name@gname,
  name@count.group.count.<b>asc</b>
};
</pre>
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
<pre>
users{
 registration@firstReg.<b>min</b>
};
</pre>
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
<pre>
groups {
  id,
  name,
  name@permission.<b>permission</b>
};
</pre>

**Object Oriented approach**
```php
$db = (new OSuQL)->query()
                  ->groups()
                    ->field('id')
                    ->field('name')
                    ->field('name', 'permission')->permission();
```
| id | name  | permission        |
|----|-------|-------------------|
| 1  | admin | can do everything |
| 2  | user  | can read only     |
| 3  | guest | can do nothing    |



## Conclusion

SuQL is all about modifiers. They already replace standart SQL clauses such as `WHERE`, `GROUP`, `JOIN`, `ORDER` etc and SQL functions.

More than that, you can develop your own modifiers.
