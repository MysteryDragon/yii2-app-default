<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email'], 'trim'],
            [['email'], 'required'],
            [['email'], 'email'],
            [['email'], 'exist',
                'targetClass' => User::className(),
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => 'Пользователя с таким email-адресом в системе не зарегистрировано.',
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $this->email,
        ]);

        if (!$user) {
            return false;
        }

        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
        }

        if (!$user->save()) {
            return false;
        }

        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->params['adminEmailName']])
            ->setTo($this->email)
            ->setSubject('Восстановление доступа на сайте ' . Yii::$app->name)
            ->send();
    }
}
