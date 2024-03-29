# Change log

## v8.0.7 - January 25, 2023

- Добавил компонент для работы с Router

---

## v8.0.6 - December 12, 2022

- Изменил лого, так как изменилась концепция фреймворка

---

## v8.0.5 - November 23, 2022

- Рефакторинг
- Произвольные выражения (переделка)

---

## v8.0.4 - November 22, 2022

- Рефакторинг

---

## v8.0.3 - August 12, 2022

- Доработки SuQL Js

---

## v8.0.2 - August 12, 2022

- Доработки SuQL Js

---

## v8.0.1 - August 12, 2022

- Первый переделанный вариант SuQL Js

---

## v8.0.0 - August 11, 2022

- Fronted side refactor

---

## v7.3.26 - July 29, 2022

- bug fixes

---

## v7.3.25 - July 29, 2022

- add get method in suql js

---

## v7.3.24 - July 29, 2022

- Update TSML

---

## v7.3.23 - July 29, 2022

- Bug fixes

---

## v7.3.22 - July 29, 2022

- Bug fixes

---

## v7.3.21 - July 27, 2022

- Завершение с sg-foreach

---

## v7.3.20 - July 27, 2022

- Поддержка sg-foreach

---

## v7.3.19 - July 27, 2022

- Refactor

---

## v7.3.18 - July 27, 2022

- Поддержка sg-model

---

## v7.3.17 - July 26, 2022

- Bug fixes

---

## v7.3.16 - July 26, 2022

- Html v0.1

---

## v7.3.15 - July 26, 2022

- Теперь SuQL - это fullstack framework. Добавил поддержку SuQL Template

---

## v7.3.14 - July 22, 2022

- Добавил триггеры на добавление записей

---

## v7.3.13 - June 24, 2022

- Рефакторинг макросов

---

## v7.3.12 - June 24, 2022

- Поддержка макросов

---

## v7.3.11 - June 17, 2022

- Добавил запрос Update
- Чтение настроек прокси через локальный конфиг NPM
- Добавил аннотации описание подключения через прокси
- Исправление ошибок

---

## v7.3.10 - June 15, 2022

- В ORM схемах всегда по умолчанию используется алгоритм smart join
- Добавил поддержку SuQL Json RPC Service

---

## v7.3.9 - June 10, 2022

Поддержка SuQL File

---

## v7.3.8 - June 9, 2022

- Поддержка буфера в TSML

---

## v7.3.7 - June 9, 2022

- Рефакторинг буфера при смешивании данных из разных СУБД

---

## v7.3.6 - June 9, 2022

- Рефакторинг
- Смешивание данных из разных СУБД

---

## v7.3.5 - June 8, 2022

- Чтение аннотаций сервиса

---

## v7.3.4 - June 8, 2022

- Добавил поддержку SuQL Service

---

## v7.3.3 - June 3, 2022

- Доработал функции постобработки по всем данным и отдельным столбцам

---

## v7.3.2 - June 3, 2022

- Закончил с SuQL Array

---

## v7.3.1 - June 2, 2022

- Исправление ошибок

---

## v7.3 - June 1, 2022

- Добавил Tsml синтаксис описания запросов
- Отрефакторил Yaml синтаксис

---

## v7.2.11 - May 31, 2022

- Починил WHERE модификатор

---

## v7.2.10 - May 31, 2022

- Доработки по SuQL Yaml синтаксису

---

## v7.2.9 - May 20, 2022

- Выпилил поддержку контроллеров

---

## v7.2.8 - May 19, 2022

- Задание имени таблицы через аннотацию

---

## v7.2.7 - May 18, 2022

- Refactor

---

## v7.2.5 - May 18, 2022

- Добавил YamlSuQL синтакс

---

## v7.2.4 - April 28, 2022

- Добавил анализатор Psalm

---

## v7.2.3 - April 6, 2022

- Добавил контроллеры

---

## v7.2.2 - April 4, 2022

- Рефакторинг

---

## v7.2.1 - March 31, 2022

- Поддержка задания связей в самих моделях
- Рефакторинг

---

## v7.2.0 - March 29, 2022

- Добавил модификатор as как еще один из вариантов для задания алиаса для поля
- Рефакторинг (выпилил обязательное задание в моделях метода fields)
- Рефакторинг (выпилил метод real из моделей, если модель задана как вьюха то всегда создается)
- Добавил метод для получения PRIMARY KEY поля модели
- Добавил метод on для ручного определения связи между моделями
- Доработка метода count
- Добавил ORM цепочки

---

## v7.1.18 - March 22, 2022

- Доработка аннотаций
- Рефакторинг

---

## v7.1.17 - March 22, 2022

- Аннотации моделей

---

## v7.1.16 - March 21, 2022

- Работа через Entity Manager
- Исправление ошибок
- Рефакторинг

---

## v7.1.15 - March 18, 2022

- Наполнение данными модели, таблицы которой нет в базе
- Пост обработку данных перенес после сериализации
- Исправление ошибок

---

## v7.1.14 - March 18, 2022

- Создание таблицы модели в базе данных если таблицы не существует

---

## v7.1.13 - March 17, 2022

- Исправление ошибок

---

## v7.1.12 - March 17, 2022

- Исправление ошибок

---

## v7.1.11 - March 15, 2022

- Update docs

---

## v7.1.10 - March 15, 2022

- Support Sqlite, Postgresql

---

## v7.1.9 - March 10, 2022

- Support Smart Date

---

## v7.1.8 - March 9, 2022

- Support Raw View
- Support Join Aliases

---

## v7.1.7 - March 4, 2022

- Add documentation

---

## v7.1.4 - March 4, 2022

- Join by named relations

---

## v7.1.3 - March 3, 2022

- Support table aliases (FROM CLAUSE)
- Refactor

---

## v7.1.2 - October 1, 2021

- Result serialization

---

## v7.1.1 - August 10, 2021

- Transactions support
- Real view support

---

## v7.1.0 - August 09, 2021

- Refactor

---

## v7.0.16 - August 06, 2021

- Bug fixes

---

## v7.0.15 - August 04, 2021

- Add not orm syntax

---

## v7.0.14 - July 04, 2021

- Fix db charset default (utf-8)

---

## v7.0.13 - July 02, 2021

- Add ORM syntax for creating new models (INSERT QUERY)
- Create a database from suql generator
- Bug fixes
- Refactor

---

## v7.0.10 - June 23, 2021

- Refactor
- Forgot to keep the change log up :)

---

## v7.0.9 - June 09, 2021

- Autoload project & models after generating
- Refactor
- Bug fixes

---

## v7.0.8 - June 08, 2021

- Add code generator
- Refactor
- Bug fixes

---

## v7.0.7 - June 04, 2021

- Smart join with tables & views
- Refactor
- Bug fixes

---

## v7.0.6 - June 03, 2021

- Refactor

---

## v7.0.5 - June 03, 2021

- ORM chain with views

---

## v7.0.4 - June 03, 2021

- Null View

---

## v7.0.3 - June 03, 2021

- Add more examples

---

## v7.0.1 - June 02, 2021

- Add smart joins

---

## v7.0.0 - May 28, 2021

- Add syntax sugar

---

## v6.0.0 - May 5, 2021

- Refactor

---

## v5.0.17 - April 26, 2021

- Add stored procedures and functions

---

## v5.0.16 - April 23, 2021

- Code generator (models, views, modifiers)

---

## v5.0.15 - April 22, 2021

- Inline modifiers in the select list

---

## v5.0.14 - April 22, 2021

- Custom placeholders

---

## v5.0.13 - April 21, 2021

- Sugar for WHERE Clause

---

## v5.0.12 - April 20, 2021

- More sugar syntax

---

## v5.0.11 - April 20, 2021

- Fix Filter View
- Refactor

---

## v5.0.10 - April 15, 2021

- Filter View

---

## v5.0.9 - April 12, 2021

- Execute select & insert queries

---

## v5.0.8 - April 12, 2021

- Add insert query
- Add select offset limit support

---

## v5.0.7 - April 10, 2021

- Support field modifiers as anonymous functions

---

## v5.0.6 - April 9, 2021

- Add join with view
- Add nested queries (view inside view)
- Remove string helper
- Add select raw expression

---

## v5.0.5 - April 5, 2021

- Refactor code structure
- Add query modifiers

---

## v5.0.4 - April 3, 2021

- Install via composer

---

## v5.0.3 - April 3, 2021

- Add install instruction

---

## v5.0.2 - April 2, 2021

**New features:**
- Support PDO

---

## v5.0.1 - April 2, 2021

**New features:**
- Add filter modifier
- Remove commands (no need anymore since we use orm approach)
- Add examples of extending SuQL syntax

---

## v5.0 - April 1, 2021

**New features:**
- Remove SuQL syntax
- New OSuQL syntax

---

## v4.0.1 - March 29, 2021

**New features:**
- New SuQL syntax
- Remove OSuQL syntax

---

## v3.4 - Aug 10, 2020

- Work on documentation

---

## v3.3 - Aug 4, 2020

**New features:**
- Prepare for submitting to composer

**Bug fixes:**
- refactor

---

## v3.2 - Jul 30, 2020

**Bug fixes:**
- refactor

---

## v3.1 - Jul 25, 2020

**New features:**
- Support commands query as PHP functions

---

## v3.0 - Jul 20, 2020

**Bug fixes:**
- refactor

---

## v2.0 - Jul 13, 2020

**New features:**
- New SuQL Syntax
- Similar tests for SuQL & OSuQL syntax
- New build system
- Add UNION support

**Bug fixes:**
- refactor

---

## v1.3.2 - Apr. 27, 2020

**Bug fixes:**
- Refactor

---

## v1.3.1 - Apr. 26, 2020

**New features:**
- Field aliases are not necessary while using field modifiers
- LIMIT clause
- DISTINCT
- Support different SQL Drivers
- SQL Custom Modifiers define in an external file
- CASE Statement
- Object Oriented approach to generate queries

**Bug fixes:**
- Where clause bitwise operations
- Sorting data gotten by the count modifier
- Refactor
- Nested queries inside the WHERE Clause

---

## v1.3 - Apr. 12, 2020

**New features:**
- Change suql join syntax

**Bug fixes:**
- Refactor

---

## v1.2 - Apr. 04, 2020

**New features:**
- SQLModifier class to process custom SuQL and base SQL functions
- Add HAVING
- Testing an SQLObject before and after preparing it
- Add default modifier handler
- Convert SuQL reserved words to SQL

**Bug fixes:**
- Refactor

---

## v1.1 - Mar. 29, 2020

**Bug fixes:**
- Use WHERE instead of HAVING
- Refactor
- Order fields weren't in select

**New features:**
- Build the library into one .phar file
- Use PHPUnit
- Function chain calling

**Added:**
- Some unit tests
- Change log

---

## v1.0 - Mar. 23, 2020

Initial release

**Added:**
- Basic SQL (SELECT, JOIN, WHERE, GROUP BY, ORDER BY)
- Nested queries
