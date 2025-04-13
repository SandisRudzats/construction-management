<?php

declare(strict_types=1);

namespace api\models;

use api\modules\Employee\models\Employee;
use Yii;
use yii\base\Model;

/**
 * @property string|null $username
 * @property string|null $password
 * @property Employee|null $user
 */
class LoginForm extends Model
{
    public string $username;
    public string $password;

    private ?Employee $user = null;

    public function rules(): array
    {
        return [
            [['username', 'password'], 'required'],
            ['password', 'validatePassword'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'username' => Yii::t('app', 'Username or Email'),
            'password' => Yii::t('app', 'Password'),
        ];
    }

    public function validatePassword(string $attribute, $params): void
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || $user->password_hash === null || !Yii::$app->security->validatePassword($this->password, $user->password_hash)) {
                $this->addError($attribute, Yii::t('app', 'Incorrect username or password.'));
            }
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return Employee|null
     */
    public function getUser(): ?Employee
    {
        if ($this->user === null) {
            $this->user = Employee::findOne(['username' => $this->username]);
        }
        return $this->user;
    }
}
