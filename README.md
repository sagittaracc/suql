<p align="center">
  <img src="/assets/images/logo.png" alt="logo"/>
</p>

<p align="center">
  <a href="README.md">
    <img src="/assets/images/en.png" alt="Read SuQL documentation in English"/>
  </a>
  <a href="README.ru.md">
    <img src="/assets/images/ru.png" alt="Читать SuQL документация на русском"/>
  </a>
</p>

# Sugar SQL

<p align="left">
  <img src="https://img.shields.io/github/v/release/sagittaracc/suql" alt="GitHub release (latest by date)"/>
  <a href="https://github.com/sagittaracc/suql/blob/master/LICENSE">
    <img src="https://img.shields.io/github/license/sagittaracc/suql" alt="GitHub license"/>
  </a>
  <a href="https://github.com/sagittaracc/suql/issues">
    <img src="https://img.shields.io/github/issues/sagittaracc/suql" alt="GitHub issues"/>
  </a>
</p>

### What is this?
SuQL is syntactic sugar for SQL.

### How does this work?
Click on the image below to see the whole demo on YouTube.
<p align="center">
  <a href="https://www.youtube.com/watch?v=9-WSjChYwn4">
    <img src="https://s7.gifyu.com/images/suql-demo-by-sagittaracc.gif" alt="Sugar SQL (SuQL) demo by sagittaracc"/>
  </a>
</p>

### Why do you need this?
1. Write queries once, use for every DBMS.
2. Write queries that are easy to read and write.
3. Expand SuQL syntax on your own. SQL isn't the limit. There's no limit really.

### How do you use this?
Example:
Show all the clients in the same place.
The pure sql would be like this:
```sql
select
  count(clients.id) as count
  group_concat(clients.id separator ':') as listId
  round(clients.lat, 4) as lat
  round(clients.lon, 4) as lon
from clients
group by clients.lat, clients.lon
having count > 1
  and lat <> '0.0000'
  and lon <> '0.0000'
```
But you can do this in a more readable way like this:
```sql
select
  clients {
    lat.round(4)              -- round up to 4 signs after the dot
       .andNotEqual('0.0000') -- zero values are not interesting for us
       .group:lat,            -- group by the result column

    lon.round(4)              -- do the same thing for longitude
       .andNotEqual('0.0000')
       .group:lon,

    id.count                  -- how many users in the same place?
      .greater(1):count,      -- we are interested only
                              -- if they are more than one in the same place

    id.implode(':'):listId    -- concat the ids
  }
;
```

## Conclusion

SuQL is all about modifiers and commands. Modifiers already replace standart SQL clauses such as `WHERE`, `GROUP`, `JOIN`, `ORDER` and SQL functions etc. Meanwhile commands can do everything that SuQL or SQL can\'t.
More than that, you can develop your own modifiers and commands.
