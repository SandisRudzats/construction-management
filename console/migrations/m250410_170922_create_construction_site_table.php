<?php

declare(strict_types=1);

use yii\db\Migration;

class m250410_170922_create_construction_site_table extends Migration
{
    private const TABLE_NAME = 'construction_sites';

    public function safeUp(): bool
    {
        if (!$this->db->getTableSchema(self::TABLE_NAME)) {
            $this->createTable(self::TABLE_NAME, [
                'id' => $this->primaryKey(),
                'name' => $this->string(255)->notNull(),
                'manager_id' => $this->integer()->null()->defaultValue(null),
                'location' => $this->string(255)->null()->defaultValue(null),
                'area' => $this->float(2)->null()->defaultValue(null),
                'required_access_level' => $this->integer()->null()->defaultValue(null),
                'start_date' => $this->date()->null()->defaultValue(null),
                'end_date' => $this->date()->null()->defaultValue(null),
                'status' => $this->string(50)->null()->defaultValue(null),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            ]);

            $this->createIndex(
                'idx-construction_site-name',
                self::TABLE_NAME,
                'name'
            );

            $this->createIndex(
                'idx-construction_site-manager_id',
                self::TABLE_NAME,
                'manager_id'
            );

            $this->addForeignKey(
                'fk-construction_site-manager_id',
                self::TABLE_NAME,
                'manager_id',
                'employees',
                'id',
                'SET NULL',
                'CASCADE'
            );
        }

        return true;
    }

    public function safeDown(): bool
    {
        if ($this->db->getTableSchema(self::TABLE_NAME, true)) {
            $this->dropTable(self::TABLE_NAME);
        }

        return true;
    }
}