<?php

declare(strict_types=1);

namespace api\modules\Employee\models;

use api\modules\AccessPass\models\AccessPass;
use api\modules\WorkTask\models\WorkTask;
use Yii;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $birth_date
 * @property string $username
 * @property string|null $password_hash
 * @property int $access_level
 * @property string $role
 * @property bool $active
 * @property int $manager_id
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property-write string $password
 * @property-read string $authKey
 * @property-read WorkTask[] $workTasks
 * @property-read AccessPass[] $accessPasses
 * @property-read Employee|null $manager
 * @property-read Employee[] $subordinates
 */
class Employee extends ActiveRecord implements IdentityInterface
{
    public const TABLE_NAME = 'employees';

    public static function tableName(): string
    {
        return self::TABLE_NAME;
    }

    public function rules(): array
    {
        return [
            [['first_name', 'last_name', 'username', 'role', 'manager_id'], 'required'],
            [['role'], 'string', 'max' => 20],
            [['first_name', 'last_name', 'username', 'password_hash'], 'string', 'max' => 255],
            [['birth_date'], 'date', 'format' => 'Y-m-d'], // Corrected date format
            [['username'], 'unique'],
            [['active'], 'boolean'],
            [['role'], 'in', 'range' => ['admin', 'manager', 'employee']],
            [['access_level'], 'integer', 'min' => 1, 'max' => 5],
            [['manager_id'], 'integer']
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'birth_date' => 'Birth Date',
            'username' => 'Username',
            'password_hash' => 'Password Hash',
            'access_level' => 'Access Level',
            'role' => 'Role',
            'active' => 'Active',
            'manager_id' => 'Manager ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function findIdentity($id): ?IdentityInterface
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null): ?IdentityInterface
    {
        return null;
    }

    public function getId(): int|string
    {
        return $this->id;
    }

    public function getAuthKey(): string
    {
        return '';
    }

    public function validateAuthKey($authKey): bool
    {
        return $this->getAuthKey() === $authKey;
    }

    public static function findByUsername(string $username): ?self
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * @throws Exception
     */
    public function setPassword(string $password): ?string
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
        return $this->password_hash;
    }

    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function getManager(): ActiveQuery
    {
        return $this->hasOne(self::class, ['id' => 'manager_id']);
    }

    public function getSubordinates(): ActiveQuery
    {
        return $this->hasMany(self::class, ['manager_id' => 'id']);
    }

    public function getWorkTasks(): ActiveQuery
    {
        return $this->hasMany(WorkTask::class, ['employee_id' => 'id']);
    }

    public function getAccessPasses(): ActiveQuery
    {
        return $this->hasMany(AccessPass::class, ['employee_id' => 'id']);
    }
}
