<?php

namespace app\models;

use yii\base\Model;
use app\models\User;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username'], 'filter', 'filter' => 'trim'],
            [['username'], 'required'],
            [['username'], 'unique', 'targetClass' => User::className(), 'message' => 'Это имя пользователя уже занято.'],
            [['username'], 'string', 'min' => 2, 'max' => 255],

            [['email'], 'filter', 'filter' => 'trim'],
            [['email'], 'required'],
            [['email'], 'email'],
            [['email'], 'string', 'max' => 255],
            [['email'], 'unique', 'targetClass' => User::className(), 'message' => 'Этот email-адрес уже занят.'],

            [['password'], 'required'],
            [['password'], 'string', 'min' => 6],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();

        return $user->save() ? $user : null;
    }
}
