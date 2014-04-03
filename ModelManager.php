<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace dektrium\user;

use yii\base\Component;

/**
 * ModelManager is used in order to create models and find users.
 *
 * @method models\User                createUser
 * @method models\Profile             createProfile
 * @method models\ResendForm          createResendForm
 * @method models\LoginForm           createLoginForm
 * @method models\RecoveryForm        createPasswordRecoveryForm
 * @method models\RecoveryRequestForm createPasswordRecoveryRequestForm
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class ModelManager extends Component
{
    /**
     * @var string
     */
    public $userClass = 'dektrium\user\models\User';

    /**
     * @var string
     */
    public $profileClass = 'dektrium\user\models\Profile';

    /**
     * @var string
     */
    public $userQueryClass = 'dektrium\user\models\UserQuery';

    /**
     * @var string
     */
    public $profileQueryClass = 'yii\db\ActiveQuery';

    /**
     * @var string
     */
    public $resendFormClass = 'dektrium\user\models\ResendForm';

    /**
     * @var string
     */
    public $loginFormClass = 'dektrium\user\models\LoginForm';

    /**
     * @var string
     */
    public $passwordRecoveryFormClass = 'dektrium\user\models\RecoveryForm';

    /**
     * @var string
     */
    public $passwordRecoveryRequestFormClass = 'dektrium\user\models\RecoveryRequestForm';

    /**
     * Finds a user by id.
     *
     * @param integer $id
     *
     * @return null|models\User
     */
    public function findUserById($id)
    {
        return $this->findUser(['id' => $id])->one();
    }

    /**
     * Finds a user by username.
     *
     * @param string $username
     *
     * @return null|models\User
     */
    public function findUserByUsername($username)
    {
        return $this->findUser(['username' => $username])->one();
    }

    /**
     * Finds a user by email.
     *
     * @param string $email
     *
     * @return null|models\User
     */
    public function findUserByEmail($email)
    {
        return $this->findUser(['email' => $email])->one();
    }

    /**
     * Finds a user either by email, or username.
     *
     * @param string $value
     *
     * @return null|models\User
     */
    public function findUserByUsernameOrEmail($value)
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return $this->findUserByEmail($value);
        }

        return $this->findUserByUsername($value);
    }

    /**
     * Finds a user by id and confirmation token
     *
     * @param integer $id
     * @param string  $token
     *
     * @return null|models\User
     */
    public function findUserByIdAndConfirmationToken($id, $token)
    {
        return $this->findUser(['id' => $id, 'confirmation_token' => $token])->one();
    }

    /**
     * Finds a user by id and recovery token
     *
     * @param integer $id
     * @param string  $token
     *
     * @return null|models\User
     */
    public function findUserByIdAndRecoveryToken($id, $token)
    {
        return $this->findUser(['id' => $id, 'recovery_token' => $token])->one();
    }

    /**
     * Finds a user
     *
     * @param  $condition
     * @return models\UserQuery
     */
    public function findUser($condition)
    {
        return $this->createUserQuery()->where($condition);
    }

    /**
     * Finds a profile by user id.
     *
     * @param integer $id
     *
     * @return null|models\Profile
     */
    public function findProfileById($id)
    {
        return $this->findProfile(['user_id' => $id])->one();
    }

    /**
     * Finds a profile
     *
     * @param  mixed $condition
     *
     * @return \yii\db\ActiveQuery
     */
    public function findProfile($condition)
    {
        return $this->createProfileQuery()->where($condition);
    }

    /**
     * Creates new query for user class.
     *
     * @return \yii\db\ActiveQuery
     */
    public function createUserQuery()
    {
        return \Yii::createObject(['class' => $this->userQueryClass, 'modelClass' => $this->userClass]);
    }

    /**
     * Creates new query for user class.
     *
     * @return \yii\db\ActiveQuery
     */
    public function createProfileQuery()
    {
        return \Yii::createObject(['class' => $this->profileQueryClass, 'modelClass' => $this->profileClass]);
    }

    /**
     * @param string $name
     * @param array $params
     * @return mixed|object
     */
    public function __call($name, $params)
    {
        $property = lcfirst(substr($name, 6)) . 'Class';
        if (isset($this->$property)) {
            $config = [];
            if (isset($params[0]) && is_array($params[0])) {
                $config = $params[0];
            }
            $config['class'] = $this->$property;

            return \Yii::createObject($config);
        }

        return parent::__call($name, $params);
    }
}