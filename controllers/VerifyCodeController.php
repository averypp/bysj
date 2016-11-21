<?php 
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\VerifyCode;
use app\models\SeaShellResult;

/*
* [社区] 入口 文件 by echo add 2016-06-12
* 所有的方法 都未做数据安全校验/权限限制 /日志记录 后续待完善
*
*/
class VerifyCodeController extends Controller{
        public function actionDemo(){
                echo "demo";
        }

        public function actionCheck(){

                $mobile = Yii::$app->request->get('mobile');
                $code = Yii::$app->request->get('code');
                if(empty($mobile) || empty($code)){
                        return SeaShellResult::error("请传入参数[手机号、验证码]");
                }
                $verifyResult = VerifyCode::checkCode($mobile,$code);
                if(!$verifyResult){
                        return SeaShellResult::error("验证码不存在 或者已失效");
                }
                return SeaShellResult::success("验证码验证通过");
        }
}

?>