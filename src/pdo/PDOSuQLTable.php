<?php

use suql\syntax\SuQLTable;

abstract class PDOSuQLTable extends SuQLTable
{
    use PDOSuQL;

    protected $data = [];

    function __construct($data = [])
    {
        if (!empty($data)) {
            $this->data = $data;
        }

        parent::__construct(array_keys($data));

        $this->createConnection();
    }

    public function save()
    {
        $stmt = $this->dbh->prepare($this->getRawSql());

        foreach ($this->data as $field => $value) {
            $stmt->bindValue(":$field", $value, $this->getPDOParamType($value));
        }

        $stmt->execute();
    }
}
