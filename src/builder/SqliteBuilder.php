<?php

namespace suql\builder;

use sagittaracc\QMap;

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
    public function getPrimaryKeyQuery($table)
    {
        return (new QMap())
            ->setQuery("SELECT t1.name AS column_name FROM pragma_table_info('$table') AS t1 WHERE t1.pk = 1")
            ->setColumn('primary', function($result){
                return $result[0]['column_name'];
            });
    }
    /**
     * @inheritdoc
     */
    public function tableExistsQuery($config, $table)
    {
        return null;
    }
}
