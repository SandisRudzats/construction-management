<?php

declare(strict_types=1);

namespace api\modules\AccessPass\models;

use api\modules\ConstructionSite\models\ConstructionSite;
use api\modules\Employee\models\Employee;
use api\modules\WorkTask\models\WorkTask;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "access_passes".
 *
 * @property int $id
 * @property int $construction_site_id
 * @property int $employee_id
 * @property int $work_task_id
 * @property string $valid_from
 * @property string $valid_to
 *
 * @property ConstructionSite $constructionSite
 * @property Employee $employee
 * @property WorkTask $workTask
 */
class AccessPass extends ActiveRecord
{
    public const REQUIRED_FIELDS = ['construction_site_id', 'employee_id', 'work_task_id', 'valid_from', 'valid_to'];
    public const TABLE_NAME = 'access_passes';

    public static function tableName(): string
    {
        return self::TABLE_NAME;
    }

    public function rules(): array
    {
        return [
            [self::REQUIRED_FIELDS, 'required'],
            [['construction_site_id', 'employee_id', 'work_task_id'], 'integer'],
            [['valid_from', 'valid_to'], 'safe'],
            [
                ['construction_site_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => ConstructionSite::class,
                'targetAttribute' => ['construction_site_id' => 'id'],
            ],
            [
                ['employee_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Employee::class,
                'targetAttribute' => ['employee_id' => 'id'],
            ],
            [
                ['work_task_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => WorkTask::class,
                'targetAttribute' => ['work_task_id' => 'id'],
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'construction_site_id' => 'Construction Site ID',
            'employee_id' => 'Employee ID',
            'work_task_id' => 'Work Task ID',
            'valid_from' => 'Valid From',
            'valid_to' => 'Valid To',
        ];
    }

    public function getConstructionSite(): ActiveQuery
    {
        return $this->hasOne(ConstructionSite::class, ['id' => 'construction_site_id']);
    }

    public function getEmployee(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'employee_id']);
    }

    public function getWorkTask(): ActiveQuery
    {
        return $this->hasOne(WorkTask::class, ['id' => 'work_task_id']);
    }
}
