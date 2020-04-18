# Change log

## v1.3.1 - Apr. 18, 2020

**New features:**
- Field aliases are not necessary while using field modifiers
- LIMIT clause

**Bug fixes:**
- Where clause bitwise operations
- Sorting data gotten by the count modifier
- Refactor

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
