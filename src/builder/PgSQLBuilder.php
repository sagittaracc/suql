<?php

namespace suql\builder;

use sagittaracc\QMap;

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
        $qmap = new QMap();
        $qmap
            ->setQuery("SELECT column_name FROM information_schema.key_column_usage WHERE table_name = '$table'")
            ->setColumn('primary', function($result){
                return $result[0]['column_name'];
            });
        return $qmap;
    }
    /**
     * @inheritdoc
     */
    public function tableExistsQuery($config, $table)
    {
        return null;
    }
    /**
     * @inheritdoc
     */
    public function createTemporaryTable($model)
    {
    }
    /**
     * @inheritdoc
     */
    public function insertIntoTable($table, $data)
    {
    }
    /**
     * @inheritdoc
     */
    public function createView($model)
    {
    }
}
