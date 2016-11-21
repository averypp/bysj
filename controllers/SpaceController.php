<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\validators\SimpleValidator;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Platform;
use app\models\User;
use yii\helpers\Json;
use app\assets\AmazonBase;
use DateTime;
use DateTimeZone;
use Exception;
class SpaceController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
    	if (Yii::$app->user->isGuest) {
    		$this -> redirect('/web/index.php?r=site&login');
            //return $this->goHome();
        }
        
        $id = YII::app()->user->id;
		$model = new User();
		$user_info= $model->findById($id);
	
        return $this->render('index',[
	            'user_info' => $user_info,
	        ]);
    }
	
    /**
     * @修改用户信息
     */
	public function actionEditUserInfo()
    {
    	
        //$email = Yii::app()->request->getParam('email');
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
     /**
     * @更改密码
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



     /**
     * @根据接收到的参数跳转相应的平台站点
     */                   
   function actionCreatejumpurl(){
   		//sp 站点id,pa平台名称
        $sp = Yii::$app->request->get('sp');
        $platform_model= new Platform();
        $site_info = $platform_model->findById($sp);
        $this->redirect($site_info['site_url']);
        
    }
    
    
 /**
     * @amazon验证接口
     */                   
   function actionAuthapp(){
   		$amazon = new AmazonBase();
   		//接口需要的参数列表，参与签名
   		$sub_pramas = [
   			'ReportType' => '_GET_MERCHANT_LISTINGS_DATA_',
   		];
   		//接口的服务和版本号，不同的接口是不一样的，参与签名
   		$service_and_version='FulfillmentInboundShipment/2010-10-01';
   		
   		$result=$amazon->request('GetServiceStatus',[],$service_and_version);//GetServiceStatus   GetReportCount
   		var_dump($result);//GetReportRequestList

    }
    
    

    
    
    function actionShowlist(){
    	 $sp = Yii::$app->request->get('sp');
    	 $platform_model= new Platform();
         $site_info = $platform_model->findByPid($sp);
         var_dump($site_info);die;
    }
    
    
	/**
     * @创建店铺
     */
    function actionCreateStore(){

        // na="+ CreateShop.shop_name + "&session_id=" + $("#session-id").val()
        //        + "&platform="+CreateShop.platform + "&sp="+CreateShop.site + "&seller_id=" + seller_id
        $data['store_name'] = Yii::$app->request->post('na');
        $data['platform_id'] = Yii::$app->request->post('platform_id');
        $data['site_id'] = Yii::$app->request->post('sp');
        $data['merchant_id'] = Yii::$app->request->post('merchant_id');
        $data['accesskey_id'] = Yii::$app->request->post('accesskey_id');
        $data['secret_key'] = Yii::$app->request->post('secret_key');
        $store_model = new Store();
        $store_id=$store_model->createStore($data);
        $return_data['success'] = false;
        if($store_id){
        	$return_data['success'] = true;
            $return_data['store_id'] = $store_id;
        }
        return Json::encode($return_data);
        
    }
    

    
}
