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
//            ['password'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'username' => Yii::t('app', 'Username or Email'),
            'password' => Yii::t('app', 'Password'),
        ];
    }
}
