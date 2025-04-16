<?php

use yii\db\Migration;

class m250415_092641_add_access_pass_database_table extends Migration
{
    private const TABLE_NAME = 'access_passes';

    public function safeUp(): void
    {
        if (!$this->db->getTableSchema(self::TABLE_NAME, true)) {
            $this->createTable(self::TABLE_NAME, [
                'id' => $this->primaryKey(),
                'construction_site_id' => $this->integer()->notNull(),
                'employee_id' => $this->integer()->notNull(),
                'work_task_id' => $this->integer()->notNull(),
                'valid_from' => $this->dateTime()->notNull(),
                'valid_to' => $this->dateTime()->notNull(),
            ]);

            $this->addForeignKey(
                'fk_access_passes_construction_site_id',
                self::TABLE_NAME,
                'construction_site_id',
                'construction_sites',
                'id',
                'CASCADE',
                'NO ACTION'
            );

            $this->addForeignKey(
                'fk_access_passes_employee_id',
                self::TABLE_NAME,
                'employee_id',
                'employees',
                'id',
                'CASCADE',
                'NO ACTION'
            );

            $this->addForeignKey(
                'fk_access_passes_work_task_id',
                self::TABLE_NAME,
                'work_task_id',
                'work_tasks',
                'id',
                'CASCADE',
                'NO ACTION'
            );
        }
    }

    public function safeDown(): void
    {
        if ($this->db->getTableSchema(self::TABLE_NAME, true)) {
            // Drop foreign keys only if they exist
            $constraintName = 'fk_access_passes_work_task_id';
            if ($this->isForeignKeyExist(self::TABLE_NAME, $constraintName)) {
                $this->dropForeignKey($constraintName, self::TABLE_NAME);
            }

            $constraintName = 'fk_access_passes_employee_id';
            if ($this->isForeignKeyExist(self::TABLE_NAME, $constraintName)) {
                $this->dropForeignKey($constraintName, self::TABLE_NAME);
            }

            $constraintName = 'fk_access_passes_construction_site_id';
            if ($this->isForeignKeyExist(self::TABLE_NAME, $constraintName)) {
                $this->dropForeignKey($constraintName, self::TABLE_NAME);
                $this->dropTable(self::TABLE_NAME);
            }
        }
    }
}
