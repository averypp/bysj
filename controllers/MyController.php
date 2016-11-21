<?php 
namespace app\controllers;
use Yii;
use yii\web\Controller;
use app\models\SeaShellResult;
use app\models\User;
use app\models\Store;



/**
* 个人中心 入口
*/
class MyController extends Controller
{
	public $enableCsrfValidation = false;//临时允许重复提交相同数据


	
	//修改用户信息（用户名/qq）
	public function actionEditUser(){
		if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }
        $userName = Yii::$app->request->post('username');
        $qq = Yii::$app->request->post('qq');
        if(empty($userName) && empty($qq)){
        	SeaShellResult::error("请传入修改的参数值[userName or qq]");
        }

        $userId = Yii::$app->session->get('user_id');
        $data["username"] = $userName;
        $data["qq"] = $qq;
        $editResult = User::editUserInfo($data,$userId);
        if($editResult){
        	return SeaShellResult::success("ok");
        }else{
        	return SeaShellResult::error("error");
        }

	}

	//修改密码
	public function actionChangePassword(){
		if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }
        $newPassword = Yii::$app->request->post('new_pw');
        $oldPassword = Yii::$app->request->post('check_pw');
		$userId = Yii::$app->session->get('user_id');

		if( empty($oldPassword) || empty($newPassword) ){
			return SeaShellResult::error("请传入参数 旧密码or新密码");
		}
		if($newPassword == $oldPassword){
			return SeaShellResult::error("password is the same");
		}
		$user_info = User::findById($userId);
		if( User::getMd5PassWord($oldPassword) != $user_info['password']){
			return SeaShellResult::error("oldpass error");
        }else {
            if (User::changePassword($newPassword,$userId)){
				return SeaShellResult::success("change success");
            }
            return SeaShellResult::error("change password error");
        }

	}

	//修改店铺名称
	public function actionChangeShopName(){
		if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }
        $shopName = Yii::$app->request->post('shopName');
        $shopId = Yii::$app->request->post('shopId');
		$userId = Yii::$app->session->get('user_id');

		if( empty($shopName) || empty($shopId) ){
			return SeaShellResult::error("请传入参数 店铺名称or店铺ID");
		}
		$editStoreName = Store::editStoreName($shopId,$userId,$shopName);
		if(!$editStoreName){
			return SeaShellResult::error("修改店铺名称 error");
		}
		return SeaShellResult::success("修改店铺名称 success");
	}

	//删除店铺
	public function actionDelShop(){
		if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }
        $shopId = Yii::$app->request->post('shopId');
		$userId = Yii::$app->session->get('user_id');
		if(empty($shopId)){
			return SeaShellResult::error("请传入参数 店铺ID");
		}
		$delStore = Store::delStore($shopId,$userId);
		if(!$delStore){
			return SeaShellResult::error("删除店铺 error");
		}
		return SeaShellResult::success("删除店铺 success");
	}



}


?>