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
 * @property string $start_date
 * @property string $end_date
 * @property ?string $description
 * @property ?string $created_at
 * @property ?string $updated_at
 *
 * @property-read Employee $employee
 * @property-read ConstructionSite $construction-site
 */
class WorkTask extends ActiveRecord
{
    public const TABLE_NAME = 'work_tasks';
    public const REQUIRED_FIELDS = ['employee_id', 'construction_site_id', 'start_date', 'end_date', 'description'];
    public static function tableName(): string
    {
        return self::TABLE_NAME;
    }

    public function rules(): array
    {
        return [
            [self::REQUIRED_FIELDS, 'required'],
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
            [['description'], 'string' , 'max' => 255],
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