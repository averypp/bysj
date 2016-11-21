<?php 

namespace app\models;
use Yii;
use yii\web\IdentityInterface;
use yii\db\ActiveRecord;

/**
* 注册发送验证码记录 的数据层
* add by echo 2016-06-17
*/
class VerifyCode extends \yii\db\ActiveRecord 
{
	public static function tableName()
    {
        return 'sea_verify_code';
    }


    /**
     * save users code by echo add 2016-06-17
     *
     * @param  string $code string $mobile
     * @return 1/0
     */
    public static function saveCode($verifyCode,$mobile)
    {
    	$verifyCodeMode = new VerifyCode();
    	$verifyCodeMode->code = $verifyCode;
    	$verifyCodeMode->mobile = $mobile;
    	$verifyCodeMode->gmt_create = date("Y-m-d H:i:s");
    	$verifyCodeMode->gmt_modified = $verifyCodeMode->gmt_create;

    	$verifyCodeMode->creator = 0;
    	$verifyCodeMode->modifier = 0;
    	$verifyCodeMode->is_deleted = "N";

    	$saveCodeResult = $verifyCodeMode->save();
        return $saveCodeResult;
    }

    /**
    * [验证 code]
    * @param $mobile:手机号 $verifyCode:验证码
    * @result false:不存在or验证码已失效 
    */
    public function checkCode($mobile,$verifyCode){
        $activeTime = date("Y-m-d H:i:s",time()-6000);
        $verifyCode = VerifyCode::find()
        ->where(['is_deleted' => 'N','mobile' => $mobile,'code'=> $verifyCode])
        ->andWhere([">","gmt_create",$activeTime])
        ->orderBy('id desc')
        ->asArray()
        ->all();       
        if(!$verifyCode){
            return false;
        }
        return true;
    }
    
}


?>