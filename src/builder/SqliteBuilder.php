<?php

namespace suql\builder;

/**
 * Sqlite сборщик
 *
 * @author sagittaracc <sagittaracc@gmail.com>
 */
final class SqliteBuilder extends SQLBuilder
{
    /**
     * @var string $quote
     */
    protected $quote = '"';
    /**
     * @var string $unquote
     */
    protected $unquote = '"';
    /**
     * @inheritdoc
     */
    protected function buildSmartDate($fieldName, $smartDate)
    {
        return '';
    }
}
