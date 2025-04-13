<?php

declare(strict_types=1);

namespace api\modules\AccessPass\models;

use api\modules\ConstructionSite\models\ConstructionSite;
use api\modules\Employee\models\Employee;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $construct_site_id
 * @property int $employee_id
 * @property string $access_level
 * @property string $date
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property-read Employee $employee
 * @property-read string $formattedDate
 * @property-read ConstructionSite $construction-site
 */
class AccessPass extends ActiveRecord
{
    public const TABLE_NAME = 'access_passes';

    public static function tableName(): string
    {
        return self::TABLE_NAME;
    }

    public function rules(): array
    {
        return [
            [['construct_site_id', 'employee_id', 'access_level', 'date'], 'required'],
            [['construct_site_id', 'employee_id'], 'integer'],
            [['date'], 'date', 'format' => 'yyyy-mm-dd'],
            [['access_level'], 'string', 'max' => 255],
            [
                ['construct_site_id'],
                'exist',
                'targetClass' => ConstructionSite::class,
                'targetAttribute' => ['construct_site_id' => 'id']
            ],
            [['employee_id'], 'exist', 'targetClass' => Employee::class, 'targetAttribute' => ['employee_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'construct_site_id' => 'Construction Site ID',
            'employee_id' => 'Employee ID',
            'access_level' => 'Access Level',
            'date' => 'Date',
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
        return $this->hasOne(ConstructionSite::class, ['id' => 'construct_site_id']);
    }

    public function getFormattedDate(): string
    {
        return Yii::$app->formatter->asDate($this->date, 'long');
    }
}