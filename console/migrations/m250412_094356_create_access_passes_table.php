<?php

declare(strict_types=1);

use yii\db\Migration;

class m250412_094356_create_access_passes_table extends Migration
{
    private const TABLE_NAME = 'access_passes';
    public function safeUp(): void
    {
        if (!$this->db->getTableSchema(self::TABLE_NAME)) {
            $this->createTable(self::TABLE_NAME, [
                'id' => $this->primaryKey(),
                'construct_site_id' => $this->integer()->notNull(),
                'employee_id' => $this->integer()->notNull(),
                'access_level' => $this->integer()->defaultValue(1),
                'date' => $this->date()->notNull(),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            ]);

            $this->createIndex(
                'idx-access_passes-construct_site_id',
                self::TABLE_NAME,
                'construct_site_id'
            );

            $this->createIndex(
                'idx-access_passes-employee_id',
                self::TABLE_NAME,
                'employee_id'
            );

            $this->createIndex(
                'idx-access_passes-access_level',
                self::TABLE_NAME,
                'access_level'
            );

            $this->addForeignKey(
                'fk-access_passes-construct_site_id',
                self::TABLE_NAME,
                'construct_site_id',
                'construction_sites',
                'id',
                'CASCADE'
            );

            $this->addForeignKey(
                'fk-access_passes-employee_id',
                self::TABLE_NAME,
                'employee_id',
                'employees',
                'id',
                'CASCADE'
            );
        }
    }
    
    public function safeDown(): void
    {
        if ($this->db->getTableSchema(self::TABLE_NAME)) {
            $this->dropForeignKey('fk-access_passes-construct_site_id', self::TABLE_NAME);
            $this->dropForeignKey('fk-access_passes-employee_id', self::TABLE_NAME);

            $this->dropIndex('idx-access_passes-construct_site_id', self::TABLE_NAME);
            $this->dropIndex('idx-access_passes-employee_id', self::TABLE_NAME);
            $this->dropIndex('idx-access_passes-access_level', self::TABLE_NAME);

            $this->dropTable(self::TABLE_NAME);
        }
    }
}
