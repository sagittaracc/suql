<?php

declare(strict_types=1);

use suql\syntax\Transaction;

final class TransactionTest extends DbTestCase
{
    public function testSuccessTransaction(): void
    {
        $success = false;

        try {
            $transaction = Transaction::begin($this->db);
            $this->db->query("insert into table_10 (f1, f2) values (100, 100)")->exec();
            $this->db->query("insert into table_10 (f1, f2) values (101, 101)")->exec();
            $success = true;
            $transaction->commit();
        } catch (Exception $e) {
            $success = false;
            $transaction->rollback();
        }

        $this->assertTrue($success);
    }

    public function testFailTransaction(): void
    {
        $success = false;

        try {
            $transaction = Transaction::begin($this->db);
            $this->db->query("insert into table_10 (f1, f2) values (100, 100)")->exec();
            $this->db->query("insert into table_10 (f1, f2, unknown_field) values (101, 'string', false)")->exec();
            $success = true;
            $transaction->commit();
        } catch (Exception $e) {
            $success = false;
            $transaction->rollback();
        }

        $this->assertFalse($success);
    }
}
