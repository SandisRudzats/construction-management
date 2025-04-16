<?php

use yii\db\Migration;

class m250414_072420_add_description_field_to_work_tasks_table extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('work_tasks', 'description', $this->text()->null()->after('employee_id'));
    }

    public function safeDown(): void
    {
        $this->dropColumn('work_tasks', 'description');
    }
}
