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

### Что это?
SuQL - это синтаксический сахар для SQL используемый языком [SuQL Script](https://github.com/sagittaracc/suql-script).

### Зачем это нужно?
1. Написание SQL запросов один раз и последующая компиляция их под нужную СУБД.
2. Создание запросов, простых для чтения и написания.
3. Возможность расширить синтаксис самостоятельно, как угодно, выходя за рамки даже SQL. Границ просто нет.

### Как это использовать?
Пример:
Показать всех клиентов находящихся в одном месте.
На чистом sql мы бы написали что-то вроде этого:
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

Используя синтаксический сахар запрос можно написать проще и удобней для чтения вот так:
```sql
select
  clients {
    lat.round(4)              -- округляем до четырех знаков после запятой
       .andNotEqual('0.0000') -- нулевые значения отбрасываем
       .group:lat,            -- группируем по результирующей колонке

    lon.round(4)              -- тоже самое делаем для долготы
       .andNotEqual('0.0000')
       .group:lon,

    id.count                  -- считаем кол-во пользователей
      .greater(1):count,      -- нас интересует только если их больше одного в одном месте

    id.implode(':'):listId    -- сцепляем id'шники
  }
;
```

## Conclusion

SuQL работает полностью на модификаторах и командах. Модификаторы заменяют такие стандартные SQL операции как  `WHERE`, `GROUP`, `JOIN`, `ORDER`,  SQL функции и т.д. Команды делают то, что чистый SQL сделать не может, или не может в рамках определенной СУБД.
Более того, Вы можете разрабатывать свои собственные модификаторы и команды.
