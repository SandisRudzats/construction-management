<?php

use yii\db\Migration;

class m250412_100358_add_fk_for_work_tasks_access_passes_to_emp_table extends Migration
{

    public function safeUp(): void
    {
        $this->addForeignKey(
            'fk-employees-work_tasks-employee_id',
            'work_tasks',
            'employee_id',
            'employees',
            'id',
            'NO ACTION',
        );

        $this->addForeignKey(
            'fk-employees-access_passes-employee_id',
            'access_passes',
            'employee_id',
            'employees',
            'id',
            'NO ACTION'
        );
    }

    public function safeDown(): void
    {
        $this->dropForeignKey(
            'fk-employees-work_tasks-employee_id',
            'work_tasks'
        );

        $this->dropForeignKey(
            'fk-employees-access_passes-employee_id',
            'access_passes'
        );
    }
}
