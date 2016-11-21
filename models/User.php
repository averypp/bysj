<?php

namespace app\models;
use Yii;
use yii\web\IdentityInterface;
use yii\db\ActiveRecord;
use app\models\LoginForm;
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    public static $_seaShellKey = "seaShellKey";//密码加密 所用
	
	public $id;
    public $authKey;
    public $accessToken;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sea_user';  // '{{user}}';

    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile', 'password', 'reg_time'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password' => 'password',
        	'mobile'   => 'mobile',
            'teg_time' => 'teg_time',
        ];
    }

     /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->$password = $password;
    }
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
        //return isset(self::$users[$id]) ? new static(self::$users[$id]) : null;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['accessToken' => $token]);
        /*foreach (self::$users as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }

        return null;*/
    }
    
	/**
     * @register mothod
     * @param  array  $register_data
     * @return true|null
     */
    public static function register($register_data)
    {
    	if($register_data){
    		$mobile   = $register_data['mobile'];
	    	$password = self::getMd5PassWord($register_data['password']); //md5(md5($register_data['password']).$_seaShellKey); 
    		$reg_time= date("Y-m-d H-i-s");
            $userModel = new User();
            $userModel->mobile = $mobile;
            $userModel->password = $password;
            $userModel->reg_time = $reg_time;
            $userModel->save();

            LoginForm::saveLoginForm($mobile,$password);
	        return true;
    	}else{
    		return null;
    	}
    	
       
    }
    
	/**
     * @register mothod
     * @param  array  $user_data 
     * @param  int    uid 用户ID
     * @return true|null
     */
    public static function editUserInfo($user_data, $id)
    {
    	
        $user = User::find()
            ->where(['id' => $id])
            ->one();
        if(!$user){
            return false;
        }
        $user->qq = $user_data['qq'];
        $user->username = $user_data['username'];
        if($user->update()){
            return true;
        }
       return false;
    }

    /**
    * 修改登录密码
    * @param  $password (明文)/$id   
    */
    public static function changePassword($password,$id){
        $user = User::find()
            ->where(['id' => $id])
            ->one();
        if(!$user){
            return SeaShellResult::error("no user");
        }

        $user->password = self::getMd5PassWord($password);
        if($user->update()){
            return true;
        }
        return false;
    }

    public static function getMd5PassWord($password){
        if(empty($password)){
            return null;
        }
        $newPassword = md5(md5($password).self::$_seaShellKey);
        return $newPassword;
    }

    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
          $user = User::find()
            ->where(['username' => $username])
            ->asArray()
            ->one();
            if($user){
            	return new static($user);
        	}

        return null;
        /*foreach (self::$users as $user) {
            if (strcasecmp($user['username'], $username) === 0) {
                return new static($user);
            }
        }

        return null;*/
    }
    
	/**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findById($id)
    {
          $user = User::find()
            ->where(['id' => $id])
            ->asArray()
            ->one();
            if($user){
            	return new static($user);
        	}

        return null;
    }
    
	/**
     * Finds user by mobile
     *
     * @param  string      $mobile
     * @return true/false
     * 注册时判断电话是否被占用
     */
    public static function findByMobile($mobile)
    {
          $user = User::find()
            ->where(['mobile' => $mobile])
            ->asArray()
            ->one();
            if($user){
            	return new static($user);
        	}
          return null;
       
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password === self::getMd5PassWord($password);
    }

}
