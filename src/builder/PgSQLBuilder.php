<?php

namespace suql\builder;

/**
 * PostgreSQL сборщик
 *
 * @author sagittaracc <sagittaracc@gmail.com>
 */
final class PgSQLBuilder extends SQLBuilder
{
    /**
     * @var string $quote
     */
    protected $quote = '"';
    /**
     * @var string $unquote
     */
    protected $unquote = '"';
}
