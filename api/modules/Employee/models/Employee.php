<?php

declare(strict_types=1);

namespace api\modules\employee\models;

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
 * @property string|null $username
 * @property string|null $password_hash
 * @property string|null $access_level
 * @property string $role
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property-write string $password
 * @property-read string $authKey
 * @property-read WorkTask[] $workTasks
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
            [['first_name', 'last_name', 'role'], 'required'],
            [['first_name', 'last_name', 'access_level', 'role', 'username', 'password_hash'], 'string', 'max' => 255],
            [['birth_date'], 'date', 'format' => 'yyyy-mm-dd'],
            [['username'], 'unique'],
            [['role'], 'in', 'range' => ['admin', 'manager', 'employee']],
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
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getWorkTasks(): ActiveQuery
    {
        return $this->hasMany(WorkTask::class, ['employee_id' => 'id']);
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
}