<?php

declare(strict_types=1);

namespace api\modules\ConstructionSite\models;

use api\modules\Employee\models\Employee;
use api\modules\WorkTask\models\WorkTask;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 * @property int $manager_id
 * @property string $location
 * @property float $area
 * @property int $required_access_level
 * @property ?string $start_date
 * @property ?string $end_date
 * @property ?string $created_at
 * @property ?string $updated_at
 *
 * @property-read WorkTask[] $workTasks
 * @property-read Employee $manager
 */
class ConstructionSite extends ActiveRecord
{
    public const TABLE_NAME = 'construction_sites';

    public static function tableName(): string
    {
        return self::TABLE_NAME;
    }

    public function rules(): array
    {
        return [
            [['manager_id', 'location', 'name', 'area', 'required_access_level', 'start_date', 'end_date'], 'required'],
            [['location', 'required_access_level'], 'string', 'max' => 255],
            [['area'], 'number'],
            [['required_access_level'], 'in', 'range' => ['low', 'medium', 'high']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'manager_id' => 'Manager ID',
            'location' => 'Location',
            'area' => 'Area',
            'required_access_level' => 'Required Access Level',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getWorkTasks(): ActiveQuery
    {
        return $this->hasMany(WorkTask::class, ['construction_site_id' => 'id']);
    }

    public function getManager(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'manager_id']);
    }
}