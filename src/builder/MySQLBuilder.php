<?php

namespace suql\builder;

use sagittaracc\ArrayHelper;
use sagittaracc\PlaceholderHelper;
use sagittaracc\QMap;

/**
 * MySQL сборщик
 *
 * @author sagittaracc <sagittaracc@gmail.com>
 */
final class MySQLBuilder extends SQLBuilder
{
    /**
     * @var string $quote
     */
    protected $quote = '`';
    /**
     * @var string $unquote
     */
    protected $unquote = '`';
    /**
     * Генерация запроса создания таблицы
     * @param \suql\syntax\ActiveRecord $model
     * @param boolean $temporary
     */
    public function createTable($model, $temporary = false)
    {
        $model->create();
        return $this->buildModel($model, $temporary);
    }
    /**
     * @inheritdoc
     */
    public function createView($model)
    {
        return "CREATE OR REPLACE VIEW `{$model->table()}` AS {$model->view()}";
    }
    /**
     * @inheritdoc
     */
    public function createTemporaryTable($model)
    {
        return $this->createTable($model, true);
    }
    /**
     * @inheritdoc
     */
    public function insertIntoTable($table, $data)
    {
        $insertIntoTableQuery = "INSERT INTO $table ({columns}) VALUES {rows}";
        $array = ArrayHelper::setArray($data);

        $header = $array->getHeader();
        $body = $array->getBody();

        $columns = implode(',', $header);
        $rows = call_user_func_array([
            (new PlaceholderHelper)->stringOfChar(count($body), '?')->setParenthesis('(', ')'),
            'bind'
        ], $body);

        return str_replace(
            ['{columns}', '{rows}'],
            [$columns, $rows],
            $insertIntoTableQuery
        );
    }
    /**
     * @inheritdoc
     */
    public function buildJoinOn($leftTable, $leftField, $rightTable, $rightField)
    {
        return "`$leftTable`.`$leftField` = `$rightTable`.`$rightField`";
    }
    /**
     * @inheritdoc
     */
    protected function buildSmartDate($fieldName, $smartDate)
    {
        $field = "`$fieldName->table`.`$fieldName->name`";

        switch ($smartDate->getType()) {
            case 'simple':
                switch ($smartDate->getPeriod()) {
                    case 'yesterday':
                        return "$field = DATE_SUB(CURDATE(), INTERVAL 1 day)";
                    case 'today';
                        return "DATE($field) = CURDATE()";
                    case 'tomorrow':
                        return "$field = CURDATE() + INTERVAL 1 DAY";
                }
                break;
            case 'ago':
                return "$field = DATE_SUB(CURDATE(), INTERVAL -{$smartDate->getNumber()} {$smartDate->getPeriod()})";
            case 'last':
                return "$field >= DATE_ADD(CURDATE(), INTERVAL -{$smartDate->getNumber()} {$smartDate->getPeriod()})";
        }
    }
    /**
     * TODO: Переименовать в buildCondition
     */
    protected function buildCompare($fieldName, $compare)
    {
        $field = "`$fieldName->table`.`$fieldName->name`";
        return "$field {$compare->getCondition()} {$compare->getValue()}";
    }
    /**
     * @inheritdoc
     */
    public function getPrimaryKeyQuery($table)
    {
        $qmap = new QMap();
        $qmap
            ->setQuery("SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'")
            ->setColumn('primary', function($result){
                return $result[0]['Column_name'];
            });
        return $qmap;
    }
    /**
     * @inheritdoc
     */
    public function tableExistsQuery($config, $table)
    {
        $query = 'select count(*) from information_schema.tables where table_schema = ? and table_name = ? limit 1';
        return (new PlaceholderHelper($query))->bind($config['dbname'], $table);
    }
}
