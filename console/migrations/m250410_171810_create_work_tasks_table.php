<?php

declare(strict_types=1);

use yii\db\Migration;

class m250410_171810_create_work_tasks_table extends Migration
{
    private const TABLE_NAME = 'work_tasks';

    public function safeUp(): bool
    {
        if (!$this->db->getTableSchema(self::TABLE_NAME)) {
            $this->createTable(self::TABLE_NAME, [
                'id' => $this->primaryKey(),
                'construction_site_id' => $this->integer()->notNull(),
                'employee_id' => $this->integer()->null()->defaultValue(null),
                'start_date' => $this->date()->null()->defaultValue(null),
                'end_date' => $this->date()->null()->defaultValue(null),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            ]);

            $this->createIndex(
                'idx-work_task-construction_site_id',
                self::TABLE_NAME,
                'construction_site_id'
            );

            $this->createIndex(
                'idx-work_task-employee_id',
                self::TABLE_NAME,
                'employee_id'
            );

            $this->addForeignKey(
                'fk-work_task-construction_site_id',
                self::TABLE_NAME,
                'construction_site_id',
                'construction_sites',
                'id',
                'NO ACTION',
                'NO ACTION'
            );

            $this->addForeignKey(
                'fk-work_task-employee_id',
                self::TABLE_NAME,
                'employee_id',
                'employees',
                'id',
                'SET NULL',
                'NO ACTION'
            );
        }
        return true;
    }

    public function safeDown(): bool
    {
        if ($this->db->getTableSchema(self::TABLE_NAME)) {
            $this->dropForeignKey('fk-work_task-construction_site_id', self::TABLE_NAME);
            $this->dropForeignKey('fk-work_task-employee_id', self::TABLE_NAME);
            $this->dropTable(self::TABLE_NAME);
        }

        return true;
    }
}