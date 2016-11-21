<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;
use yii\db\ActiveRecord;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class LoginForm extends \yii\db\ActiveRecord
{
    public $mobile;
    public $password;
    public $rememberMe = true;

    private $_user = false;

    public static function tableName()
    {
        return 'sea_login_form';  // '{{user}}';

    }

     /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['mobile', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    public static function saveLoginForm($mobile,$password){
        $loginFormModel = new LoginForm();
        $loginFormModel->mobile = $mobile;
        $loginFormModel->password = $password;
        $opt_time= date("Y-m-d H-i-s");
        $loginFormModel->gmt_create = $opt_time;
        $loginFormModel->gmt_modified = $opt_time;
        
        try {
            $loginFormModel->save();
        } catch (Exception $e) {
            print_r($e);            
        }
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
       // print_r($this->validate());
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        }
        //echo "false==<";
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByMobile($this->mobile);
        }

        return $this->_user;
    }
    public function setAttribute($mobile, $password){
        $this->mobile = $mobile;
        $this->password = $password;
    }
}
