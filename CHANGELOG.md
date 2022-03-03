# Change log

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
