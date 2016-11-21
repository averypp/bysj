<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\data\Pagination;

use app\models\User;
use app\models\Store;
use app\models\VerifyCode;
use app\models\SeaShellResult;

/*
* 用户 入口 文件 by echo add 2016-06-12
* 所有的方法 都未做数据安全校验/权限限制 /日志记录 后续待完善
*
*/
class UserController extends Controller{
    public $layout=false; //重写属性，默认是加载layouts\main.php   这里不加载
    public $enableCsrfValidation = false;//临时允许重复提交相同数据

    function actionIndex(){
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }
        $userId = Yii::$app->session->get('user_id');
        $shops = Store::getStores($userId);
        $userInfo = User::findById($userId);
        $view = "index";
        return  $this->render($view, [
                    'userInfo' => $userInfo,
                    'shops' => $shops,
                ]);
    }

    /*
    * [修改接口一：修改用户名和QQ] by echo add 2016-06-12 （todo 逻辑再看看）
    */
    function actionEdit(){
        $username = Yii::app()->request->getParam('username');
        $qq = Yii::app()->request->getParam('qq');
        $id = YII::app()->user->id;
        if ($username && $qq){
            $data['username']=$username;
            $data['qq']=$qq;
            $model = new User();
            if ($model->editUserInfo($data,$id)){
                return Json::encode(['msg'=>'success']);die;
            }
        }
    }

    /*
    * [修改接口二：更换密码] by echo add 2016-06-12(todo 逻辑有缺陷)
    */
    function actionChangePassword(){
        $oldpass = Yii::app()->request->getParam('oldpass');
        $password = Yii::app()->request->getParam('password');
        $repassword = Yii::app()->request->getParam('repassword');
        $id = YII::app()->user->id;
        if($oldpass && $password && $repassword){
            $model = new User();
            $user_info= $model->findById($id);
            if(md5($oldpass) != $user_info['password']){
                return Json::encode(['msg'=>'oldpass error']);die;
            } elseif ($password != $repassword){
                return Json::encode(['msg'=>'password not the same']);die;
            } else {
                $data['password']=md5($password);
                if ($model->editUserInfo($data,$id)){
                    return Json::encode(['msg'=>'change success']);die;
                }
            }
        }else{
            return Json::encode(['msg'=>'data miss']);die;
        }
    }

    /*
    * [查询接口三：分页查询] by echo add 2016-06-12
    */
    function actionList(){

    }

    /**
    * [注册校验 手机号：校验什么呢？是否存在系统？状态是否被冻结]
    */
    function actionVerifyMobile(){

        $mobile = Yii::$app->request->get('mobile');   // mobile
        $model = new User();
        $exitMobile = $model->findByMobile($mobile);
        if($model->findByMobile($mobile) != null ){
            $message = "have this user";
            return SeaShellResult::error($message);
        }
        return SeaShellResult::success("have no this user");
    }

    //reset密码
    public function actionResetPassword(){
        
        $mobile = Yii::$app->request->post('mobile');
        $password = Yii::$app->request->post('password');
        $code = Yii::$app->request->post('code');

        if( empty($mobile) || empty($password) ){
            return SeaShellResult::error("请传入参数 手机号or密码");
        }
        
        $user_info = User::findByMobile($mobile);
        if(!$user_info){
            return SeaShellResult::error("user no exit");
        }

        if(!VerifyCode::checkCode($mobile,$code)){
            return SeaShellResult::error("验证码不存在or验证码已失效 ");
        }

        if (User::changePassword($password,$user_info['id'])){
            return SeaShellResult::success("change success");
        }
        return SeaShellResult::error("change password error");

    }

}

?>