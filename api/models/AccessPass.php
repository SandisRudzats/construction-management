<?php

declare(strict_types=1);

namespace api\models;

use api\modules\ConstructionSite\models\ConstructionSite;
use api\modules\Employee\models\Employee;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $employee_id
 * @property int $construction_site_id
 * @property string|null $valid_from
 * @property string|null $valid_until
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Employee $employee
 * @property ConstructionSite $constructionSite
 */
class AccessPass extends ActiveRecord
{
    public const TABLE_NAME = 'access_pass';

    public static function tableName(): string
    {
        return self::TABLE_NAME;
    }

    public function rules(): array
    {
        return [
            [['employee_id', 'construction_site_id'], 'required'],
            [['employee_id', 'construction_site_id'], 'integer'],
            [['valid_from', 'valid_until'], 'safe'], // Use 'safe' for date/time fields initially
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
            'valid_from' => 'Valid From',
            'valid_until' => 'Valid Until',
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