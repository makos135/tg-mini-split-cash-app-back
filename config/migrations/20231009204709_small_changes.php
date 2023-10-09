<?php

use Phinx\Db\Adapter\MysqlAdapter;

class SmallChanges extends Phinx\Migration\AbstractMigration
{
    public function change()
    {
        $this->table('rooms', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_0900_ai_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('currency', 'string', [
                'null' => true,
                'limit' => 5,
                'collation' => 'utf8mb4_0900_ai_ci',
                'encoding' => 'utf8mb4',
                'after' => 'description',
            ])
            ->changeColumn('created', 'timestamp', [
                'null' => true,
                'default' => 'CURRENT_TIMESTAMP',
                'after' => 'currency',
            ])
            ->save();
        $this->table('transactions', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_0900_ai_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('description', 'text', [
                'null' => true,
                'limit' => 65535,
                'collation' => 'utf8mb4_0900_ai_ci',
                'encoding' => 'utf8mb4',
                'after' => 'value',
            ])
            ->changeColumn('currency', 'string', [
                'null' => true,
                'limit' => 5,
                'collation' => 'utf8mb4_0900_ai_ci',
                'encoding' => 'utf8mb4',
                'after' => 'description',
            ])
            ->changeColumn('created', 'timestamp', [
                'null' => true,
                'default' => 'CURRENT_TIMESTAMP',
                'after' => 'currency',
            ])
            ->save();
        $this->table('users', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_0900_ai_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('token', 'string', [
                'null' => true,
                'limit' => 512,
                'collation' => 'utf8mb4_0900_ai_ci',
                'encoding' => 'utf8mb4',
                'after' => 'name',
            ])
            ->changeColumn('created', 'timestamp', [
                'null' => true,
                'default' => 'CURRENT_TIMESTAMP',
                'after' => 'token',
            ])
            ->addIndex(['token'], [
                'name' => 'users_pk',
                'unique' => true,
            ])
            ->save();
    }
}
