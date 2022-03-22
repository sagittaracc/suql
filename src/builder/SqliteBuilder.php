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
    public function buildJoinOn($leftTable, $leftField, $rightTable, $rightField)
    {
        return '';
    }
    /**
     * @inheritdoc
     */
    protected function buildSmartDate($fieldName, $smartDate)
    {
        return '';
    }
    /**
     * @inheritdoc
     */
    public function tableExistsQuery($config, $table)
    {
        return null;
    }
}
