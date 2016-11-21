<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\assets\util\SimpleValidator;
use app\models\User;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\VerifyCode;
use app\assets\yunpian\lib;
use app\models\SeaShellResult;
use app\models\Community;
use app\models\Notice;
use app\models\Store;
use app\models\Platform;

use yii\helpers\Json;

use app\service\SmsService;

class SiteController extends Controller
{
    public $layout=false; //重写属性，默认是加载layouts\main.php   这里不加载
    public $enableCsrfValidation = false;//临时允许重复提交相同数据
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout','register'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['register'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    //'logout' => ['post'],
                    'register' => ['post'],
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
            return $this->redirect(['site/login']);
        }

        $userId = Yii::$app->session->get('user_id');

        $communitys = Community::getCommunitys();
        $notices = Notice::getNewNotices();
        $stores = Store::getStores($userId);

        $platforms = Platform::findByPid(0) ?: [];
        $first = reset($platforms);
        $sites = [];
        if ($first) {
            $sites = Platform::findByPid($first->id) ?: [];
        }

        return $this->render('index', [
            'communitys' => $communitys,
            'notices' => $notices,
            'stores' => $stores,
            'platforms' => $platforms,
            'sites' => $sites,
        ]);
    }

	public function actionSignup()
	{
	    $model = new SignupForm();
	    if ($model->load(Yii::$app->request->post())) {
	        if ($user = $model->signup()) {
	           // $login = new SiteLoginForm();
	            if(Yii::$app->getUser()->login($user)) {
	                return $this->goHome();
	            }
	            else
	            {
	                var_dump($user);
	            }
	        }
	    }
	    return $this->render('signup', [
	        'model' => $model,
	    ]);
	}
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if($_POST){
            $login_data = Yii::$app->request->post();
            $model->setAttribute($login_data['mobile'], $login_data['password']);
            if ($model->login()) {
                $session = Yii::$app->session;
                $session["user_id"] = Yii::$app->user->id;
                return SeaShellResult::success('/?r=site/index');
            } else {
                return SeaShellResult::error('login error');
            }
        } else {
            return $this->render('login_new', [
                'model' => $model,
            ]);
        }
    }


    public function actionLogout()
    {
        Yii::$app->user->logout();
        //unset(Yii::app()->session['user_id']);
        return $this->redirect(['site/login']);
    }

	public function actionRegister()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
		$model = new User();
        if (Yii::$app->request->post()) {
            $mobile = Yii::$app->request->post('mobile');
        	$password = Yii::$app->request->post('password');
            if( empty($mobile) || empty($password) ){
                return SeaShellResult::error("mobile/password missed");
            }
            $register_data["mobile"] = $mobile;
            $register_data["password"] = $password;
	        if ($model->findByMobile($mobile)) {
                return SeaShellResult::error("mobile existed");
	        } else {
	        	if($model->register($register_data)){
	        		return SeaShellResult::success("register ok!");
	        	}
	        }
        }else {
	        return $this->render('register', [
	            'model' => $model,
	        ]);
        }
    }
	public function actionPwd()//AjaxGetbackPwd()
    {
        $session = Yii::$app->session;
        $captcha_session=$session->get("'13148306647'");
        var_dump($captcha_session);die;
        $mobile = Yii::$app->request->get('mobile');
        $newPassword = Yii::$app->request->get('newPassword');
        $captcha = Yii::$app->request->get('captcha');
        //var_dump($captcha);die;
        $session = Yii::$app->session;
        $captcha_session=$session->get("'$mobile'");
    	if($mobile && $newPassword && $captcha && $captcha_session){
	    	if ($captcha != $captcha_session['captcha']){
	    		echo "captcha error";die;
	    	} elseif ( time() - $captcha_session['send_time'] > 15*60*60 ){
	    		echo "captcha out time";die;
	    		unset($session["'$mobile'"]);
	    	} else {
	    		$newPassword = md5($newPassword);
	    		Yii::$app->db->createCommand("update yii_user set password='$newPassword' where mobile='$mobile')")->query();
	    		unset($session["'$mobile'"]);
	    		echo "get back success";die;
	    		//echo CJSON::encode(array('val'=>$model->remark));
	    		$this -> redirect('/web/index.php?r=site&login');
	    	}
    	}else{
    		echo "data missed";die;
    	}
    }
    /**
    * 发送验证码 ()
    */
	public function actionGetCode()//AjaxSendCaptcha()
    {
    	$session = Yii::$app->session;
    	//$language = $session->get('13148306647');
    	//var_dump($language);die;
        $mobile = Yii::$app->request->get('mobile');   // mobile
        $model = new User();
        // if($model->findByMobile($mobile) != null ){
        // 	echo 'have no this user';die;
        // }
        //验证什么的
        if($mobile && Yii::$app->simpleValidator->mobile($mobile) ){
            $smsService = new SmsService();
            $result = $smsService->send($mobile);

            if ($result['success']){
            	$session = Yii::$app->session;
            	$session["'$mobile'"] = [
				    'captcha' =>$result['code'],
				    'send_time' => time(),
				];
                try {
                    VerifyCode::saveCode($result['code'],$mobile);
                } catch (Exception $e) {
                    $message=$e->getMessage();//异常信息
                    Yii::error("注册发送验证码记录数据库异常：".$message,__METHOD__);//建议这种
                }
                return SeaShellResult::success($result);
			}else{
                return SeaShellResult::error($result['msg']);
			}
        }
    }
    function sendMsgToMobile($data){
    		include_once '../assets/yunpian/YunpianAutoload.php';
			$tplOperator = new \app\assets\yunpian\lib\TplOperator();
			$result = $tplOperator->get_default(array("tpl_id"=>'2'));
        	$smsOperator = new \app\assets\yunpian\lib\SmsOperator();
			$result = $smsOperator->single_send($data);
    		return $result;
    }



    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    public function actionAbout()
    {

        $url = 'https://www.amazon.com/gp/offer-listing/B002SVPAMW';
        // $url = 'https://www.baidu.com/';
        // $url = 'http://blog.sina.com.cn/s/blog_4077692e0100qjkp.html';
        $snoopy = new \app\libraries\Snoopy();
        $snoopy->agent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/51.0.2704.79 Chrome/51.0.2704.79 Safari/537.36';
        $snoopy->referer = '';
        $snoopy->proxy_host = '133.130.96.188';
        $snoopy->proxy_port = '3128';
        $snoopy->proxy_user = 'haibeike';
        $snoopy->proxy_pass = 'proxy_haibeike';
        $snoopy->fetch($url);

        echo $snoopy->results;

        die;
        return $this->render('about');
    }

    public function actionTest()
    {
        // $badService = new \app\service\BadReviewCollectService();

        // $asin = 'B019XX9I3O';
        // $asin = 'B016ZNRC0Q';
        // $data = $badService->getMultiData($asin);
        
        // $asin = 'B004NNUX66';
        // $data = $badService->getNewestReview($asin);
        // var_dump($data);

        // \yii\helpers\ArrayHelper::multisort($data, 'review_date', SORT_DESC);

        // var_dump($data);

        // $url = 'https://www.amazon.com/Butterfly-Garden-Dot-Hutchison-ebook/product-reviews/B019XX9I3O/ref=cm_cr_getr_d_paging_btm_2?ie=UTF8&showViewpoints=1&sortBy=recent&filterByStar=three_star&pageNumber=2';

        // $ret = \app\libraries\MyHelper::request($url);
        // var_dump($ret);
    }

}
