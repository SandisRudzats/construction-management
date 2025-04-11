<?php

declare(strict_types=1);

namespace api\modules\WorkTask\models;

use api\modules\ConstructionSite\models\ConstructionSite;
use api\modules\Employee\models\Employee;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $construction_site_id
 * @property int $employee_id
 * @property string|null $start_date
 * @property string|null $end_date
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property-read Employee $employee
 * @property-read ConstructionSite $constructionSite
 */
class WorkTask extends ActiveRecord
{
    public const TABLE_NAME = 'work_tasks';

    public static function tableName(): string
    {
        return self::TABLE_NAME;
    }

    public function rules(): array
    {
        return [
            [['employee_id', 'construction_site_id'], 'required'],
            [['employee_id', 'construction_site_id'], 'integer'],
            [['start_date', 'end_date'], 'safe'], // Consider using 'date' validator with a specific format
            [
                ['employee_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Employee::class,
                'targetAttribute' => ['employee_id' => 'id']
            ],
            [
                ['construction_site_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => ConstructionSite::class,
                'targetAttribute' => ['construction_site_id' => 'id']
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'employee_id' => 'Employee ID',
            'construction_site_id' => 'Construction Site ID',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getEmployee(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'employee_id']);
    }

    public function getConstructionSite(): ActiveQuery
    {
        return $this->hasOne(ConstructionSite::class, ['id' => 'construction_site_id']);
    }
}