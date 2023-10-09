<?php

use Phinx\Db\Adapter\MysqlAdapter;

class Init extends Phinx\Migration\AbstractMigration
{
    public function change()
    {
        $this->execute("ALTER DATABASE CHARACTER SET 'utf8mb4';");
        $this->execute("ALTER DATABASE COLLATE='utf8mb4_unicode_ci';");
        $this->table('rooms', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'identity' => true,
            ])
            ->addColumn('name', 'string', [
                'null' => false,
                'limit' => 255,
                'collation' => 'utf8mb4_unicode_ci',
                'encoding' => 'utf8mb4',
                'after' => 'id',
            ])
            ->addColumn('description', 'text', [
                'null' => true,
                'limit' => 65535,
                'collation' => 'utf8mb4_unicode_ci',
                'encoding' => 'utf8mb4',
                'after' => 'name',
            ])
            ->addColumn('created', 'timestamp', [
                'null' => true,
                'default' => 'CURRENT_TIMESTAMP',
                'after' => 'description',
            ])
            ->create();
        $this->table('rooms_users', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'identity' => true,
            ])
            ->addColumn('room_id', 'integer', [
                'null' => true,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'id',
            ])
            ->addColumn('user_id', 'integer', [
                'null' => true,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'room_id',
            ])
            ->addColumn('created', 'timestamp', [
                'null' => true,
                'default' => 'CURRENT_TIMESTAMP',
                'after' => 'user_id',
            ])
            ->addIndex(['room_id'], [
                'name' => 'rooms_users_rooms_id_fk',
                'unique' => false,
            ])
            ->addIndex(['user_id'], [
                'name' => 'rooms_users_users_id_fk',
                'unique' => false,
            ])
            ->create();
        $this->table('transactions', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'identity' => true,
            ])
            ->addColumn('room_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'id',
            ])
            ->addColumn('by_user_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'room_id',
            ])
            ->addColumn('for_user_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'by_user_id',
            ])
            ->addColumn('value', 'decimal', [
                'null' => true,
                'precision' => 10,
                'scale' => 2,
                'after' => 'for_user_id',
            ])
            ->addColumn('currency', 'string', [
                'null' => true,
                'limit' => 5,
                'collation' => 'utf8mb4_unicode_ci',
                'encoding' => 'utf8mb4',
                'after' => 'value',
            ])
            ->addColumn('created', 'timestamp', [
                'null' => true,
                'default' => 'CURRENT_TIMESTAMP',
                'after' => 'currency',
            ])
            ->addIndex(['room_id'], [
                'name' => 'transactions_rooms_id_fk',
                'unique' => false,
            ])
            ->addIndex(['for_user_id'], [
                'name' => 'transactions_users__fk2',
                'unique' => false,
            ])
            ->addIndex(['by_user_id'], [
                'name' => 'transactions_users_id_fk',
                'unique' => false,
            ])
            ->create();
        $this->table('users', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'identity' => true,
            ])
            ->addColumn('telegram_id', 'string', [
                'null' => false,
                'limit' => 1024,
                'collation' => 'utf8mb4_unicode_ci',
                'encoding' => 'utf8mb4',
                'after' => 'id',
            ])
            ->addColumn('name', 'string', [
                'null' => true,
                'limit' => 255,
                'collation' => 'utf8mb4_unicode_ci',
                'encoding' => 'utf8mb4',
                'after' => 'telegram_id',
            ])
            ->addColumn('created', 'timestamp', [
                'null' => true,
                'default' => 'CURRENT_TIMESTAMP',
                'after' => 'name',
            ])
            ->create();
    }
}
