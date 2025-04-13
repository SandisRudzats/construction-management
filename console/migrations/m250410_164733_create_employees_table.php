<?php

declare(strict_types=1);

use yii\db\Migration;

class m250410_164733_create_employees_table extends Migration
{
    private const TABLE_NAME = 'employees';

    public function safeUp(): void
    {
        if (!$this->db->getTableSchema(self::TABLE_NAME, true)) {
            $this->createTable(self::TABLE_NAME, [
                'id' => $this->primaryKey(),
                'first_name' => $this->string(255)->notNull(),
                'last_name' => $this->string(255)->notNull(),
                'birth_date' => $this->date()->null()->defaultValue(null),
                'username' => $this->string(255)->unique()->notNull(),
                'password_hash' => $this->string(255)->null()->defaultValue(null),
                'access_level' => $this->integer()->defaultValue(1),
                'role' => $this->string(20)->notNull(),
                'active' => $this->boolean()->defaultValue(1),
                'manager_id' => $this->integer()->null()->defaultValue(null),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            ]);

            $this->createIndex(
                'idx-employee-username',
                self::TABLE_NAME,
                'username'
            );

            $this->createIndex(
                'idx-employee-role',
                self::TABLE_NAME,
                'role'
            );

            $this->createIndex(
                'idx-employees-manager_id',
                self::TABLE_NAME,
                'manager_id'
            );
        }
    }

    public function safeDown(): void
    {
        if ($this->db->getTableSchema(self::TABLE_NAME, true)) {
            $this->dropIndex('idx-employees-manager_id', self::TABLE_NAME);
            $this->dropIndex('idx-employee-role', self::TABLE_NAME);
            $this->dropIndex('idx-employee-username', self::TABLE_NAME);
            $this->dropTable(self::TABLE_NAME);
        }
    }
}