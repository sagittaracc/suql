# Sugar SQL
### What is this?
SuQL is syntactic sugar for SQL.
### Why do you need this?
1. Write queries easier and faster
2. Read queries in one breath
3. Make your own SuQL functions. SQL is your only limit.
### Documentation
[Learn more about SuQL](https://github.com/sagittaracc/suql/wiki)
### How do you use this?
1. Include `dist/suql.phar` in your project
2. Call `SuQL::toSql(<string>, 'mysql')` to convert an SuQL query into standart SQL (MySQL, PostgreSQL, Oracle etc.)
