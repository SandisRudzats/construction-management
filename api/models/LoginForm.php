<?php

declare(strict_types=1);

namespace api\models;

use Yii;
use yii\base\Model;
use api\modules\employee\models\Employee; // Still need the model for type hinting in getUser

/**
 * @property string|null $username
 * @property string|null $password
 * @property bool $rememberMe
 * @property Employee|null $_user
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = false;

    private ?Employee $_user = null;

    public function rules(): array
    {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'username' => Yii::t('app', 'Username or Email'),
            'password' => Yii::t('app', 'Password'),
            'rememberMe' => Yii::t('app', 'Remember me'),
        ];
    }

    public function validatePassword(string $attribute, $params): void
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !Yii::$app->security->validatePassword($this->password, $user->password_hash)) {
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
        if ($this->_user === null) {
            $this->_user = Employee::findByUsername($this->username); // Direct model interaction for now, AuthService will handle retrieval
        }
        return $this->_user;
    }
}