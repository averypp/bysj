<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\validators\SimpleValidator;
use app\models\GoodsInfo;
use app\models\ContactForm;
use app\models\Platform;
use app\models\Category;
use app\models\Specifics;
use app\models\ProductType;
use app\models\AmazonBtg;
use app\models\AmazonFeedValues;
use app\models\AmazonFeedTplData;
use app\models\SeaShellResult;
use app\models\User;
use app\assets\AmazonBase;
use app\assets\amazon\MarketplaceWebService_Client;
use DateTime;
use DateTimeZone;
use Exception;

class CreateController extends Controller
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
    //if login
    public function actionIndex()
    {
      //var_dump(Yii::$app->user);die;
    	  if (Yii::$app->user->isGuest) {
    		  $this -> redirect('/web/index.php?r=site&login');
            //return $this->goHome();
        }
        $model = new GoodsInfo();
        $product_data = Yii::$app->request->post('product');
        $product_id = $model->creategoods($product_data);
        var_dump($product_id);die;
        $id = YII::app()->user->id;
    		$model = new User();
    		$user_info= $model->findById($id);
        //return $this->render('index',[
	    //        'user_info' => $user_info,
	    //    ]);
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


//https://www.actneed.com/auth/send?na=fgdgdfgdfgg&sp=45&pa=Amazon&ts=1466388640782
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
    public function actionShowlist()
    {
        $sp = Yii::$app->request->get('sp');
        $platform_model= new Platform();
        $site_info = $platform_model->findByPid($sp);
        if(!$site_info){
        return SeaShellResult::error("no data");
      }else{
        return SeaShellResult::success($site_info);
      }
    }

  //父级分类展示，及查找下一级分类，第一级parent_id=0
    function actionGet_category(){
      $amazonBtg_model = new AmazonBtg();
      $parent_id = 0;//Yii::$app->request->get('parent_id');
      $site_id = 29;//Yii::$app->request->get('site_id');
      $cat_data=$amazonBtg_model->getCategory($site_id ,$parent_id);//($site_id ,$parent_id)
      if(!$cat_data){
        return SeaShellResult::error("no data");
      }else{
        return SeaShellResult::success($cat_data);
      }
    }

  //查询变体信息（actneed 的productType项目）
    function actionGet_variation($tpl_id =2,$site_id =29){
      $AmazonFeedValues_model = new AmazonFeedValues();
      $variation_data = $AmazonFeedValues_model->getVariation($tpl_id ,$site_id);
      if($variation_data){
        //var_dump($variation_data);die;
        return json_encode($variation_data);
      } else {
        return json_encode(["status"=>false,"msg"=>"no this categories"]);
      }
    }

    //查询分类下的信息(选择一个productType)，包含变体信息和必须商品参数
    function actionGet_specifics($tpl_id = 2, $site_id =29 ,$type = 'Accessory'){
      $AmazonFeedValues_model = new AmazonFeedValues();
      $variationThemes_data = $AmazonFeedValues_model->getVariationThemes($tpl_id ,$site_id ,$type);
      $AmazonFeedTplData_model = new AmazonFeedTplData();
      $required_attr = $AmazonFeedTplData_model->getRequiredField($tpl_id, $site_id);
      $required_attr['VariationTheme'] = $variationThemes_data;
      if(!$required_attr){
        return SeaShellResult::error("no data");
      }else{
        return SeaShellResult::success($required_attr);
      }
    }
 /**
     * @amazon验证接口,from biaoge
     */
   function actionAuthapp(){
   		$amazon = new AmazonBase();
      $AccessKeyID = Yii::$app->request->post('AccessKeyID');
      $SellerID = Yii::$app->request->post('SellerID');
      $SecretKey = Yii::$app->request->post('SecretKey');

     // $amazon ->setAttibutes('AccessKeyID', $ccessKeyID);
     // $amazon ->setAttibutes('SellerID', $SellerID);
    //  $amazon ->setAttibutes('SecretKey', $SecretKey);
      //接口需要的参数列表，参与签名
   		$sub_pramas = [
   			'ReportType' => '_GET_MERCHANT_LISTINGS_DATA_',
   		];

   		//接口的服务和版本号，不同的接口是不一样的，参与签名
   		$service_and_version='';
   		$result=$amazon->request('GetReportRequestCount',[],$service_and_version);
   		var_dump($result);die;
      if ( isset($result['Error']) && $result['Error']) {
        return SeaShellResult::error('账户信息错误');
      }else{
        return SeaShellResult::success('账户信息正确');
      }

    }

    function actionTest_amazon_sdk(){


        include_once ('../assets/amazon/MarketplaceWebService/Samples/.config.inc.php'); 

        $serviceUrl = "https://mws.amazonservices.com";

        $config = array (
          'ServiceURL' => $serviceUrl,
          'ProxyHost' => null,
          //'ProxyPort' => -1,
          'MaxErrorRetry' => 3,
        );

        $service= new \app\assets\amazon\MarketplaceWebService_Client(
             AWS_ACCESS_KEY_ID, 
             AWS_SECRET_ACCESS_KEY, 
             $config,
             APPLICATION_NAME,
             APPLICATION_VERSION);


$feed = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<AmazonEnvelope xsi:noNamespaceSchemaLocation="amzn-envelope.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <Header>
        <DocumentVersion>1.01</DocumentVersion>
        <MerchantIdentifier>M_MWSTEST_49045593</MerchantIdentifier>
    </Header>
    <MessageType>OrderFulfillment</MessageType>
    <Message>
        <MessageID>1</MessageID>
        <OperationType>Update</OperationType>
        <OrderFulfillment>
            <AmazonOrderID>002-3275191-2204215</AmazonOrderID>
            <FulfillmentDate>2009-07-22T23:59:59-07:00</FulfillmentDate>
            <FulfillmentData>
                <CarrierName>Contact Us for Details</CarrierName>
                <ShippingMethod>Standard</ShippingMethod>
            </FulfillmentData>
            <Item>
                <AmazonOrderItemCode>42197908407194</AmazonOrderItemCode>
                <Quantity>1</Quantity>
            </Item>
        </OrderFulfillment>
    </Message>
</AmazonEnvelope>
EOD;

        $marketplaceIdArray = array("Id" => array('ATVPDKIKX0DER','A2EUQ1WTGCTBG2'));
        $feedHandle = @fopen('php://temp', 'rw+');
        fwrite($feedHandle, $feed);
        rewind($feedHandle);
        $parameters = array (
          'Merchant' => MERCHANT_ID,
          'MarketplaceIdList' => $marketplaceIdArray,
          'FeedType' => '_POST_ORDER_FULFILLMENT_DATA_',
          'FeedContent' => $feedHandle,
          'PurgeAndReplace' => false,
          'ContentMd5' => base64_encode(md5(stream_get_contents($feedHandle), true)),
         //'MWSAuthToken' => '<MWS Auth Token>', // Optional
        );
        rewind($feedHandle);
        $request = new \app\assets\amazon\MarketplaceWebService_Model_SubmitFeedRequest($parameters);
        invokeSubmitFeed($service, $request);

        @fclose($feedHandle);

        }


        //重写数组转xml方法
        function actionToXMLFragment($data)
        {
            $xml = '';
            if($data && is_array($data)){
                foreach ($data as $key => $value) {
                    if (!is_null($value)) {
                        if (is_array($value)) {
                            $xml .= "<$key>";
                            $xml .= toXMLFragment($value);
                            $xml .= "</$key>";
                        } else {
                            $xml .= "<$key>";
                            $xml .= $value;
                            $xml .= "</$key>";
                        }
                    }
                }
            }
            return $xml;
        }


        /**
         * Escape special XML characters
         * @return string with escaped XML characters
         */
         function actionEscapeXML($str)
        {
            $from = array( "&", "<", ">", "'", "\"");
            $to = array( "&amp;", "&lt;", "&gt;", "&#039;", "&quot;");
            return str_replace($from, $to, $str);
        }
     /**
         * 将xml转为array
         * @param string $xml
         * @throws WxPayException
         */
     function actionFromXml($xml)
        {
            if(!$xml){
                throw new Exception("xml数据异常！");
            }
            //将XML转为array
            //禁止引用外部xml实体
            libxml_disable_entity_loader(true);
            $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
            return $values;
        }
    /**
      * Submit Feed Action Sample
      * Uploads a file for processing together with the necessary
      * metadata to process the file, such as which type of feed it is.
      * PurgeAndReplace if true means that your existing e.g. inventory is
      * wiped out and replace with the contents of this feed - use with
      * caution (the default is false).
      *
      * @param MarketplaceWebService_Interface $service instance of MarketplaceWebService_Interface
      * @param mixed $request MarketplaceWebService_Model_SubmitFeed or array of parameters
      */
      function actionInvokeSubmitFeed(MarketplaceWebService_Interface $service, $request) 
      {
          try {
                  $response = $service->submitFeed($request);
                    echo ("Service Response\n");
                    echo ("=============================================================================\n");

                    echo("        SubmitFeedResponse\n");
                    if ($response->isSetSubmitFeedResult()) { 
                        echo("            SubmitFeedResult\n");
                        $submitFeedResult = $response->getSubmitFeedResult();
                        if ($submitFeedResult->isSetFeedSubmissionInfo()) { 
                            echo("                FeedSubmissionInfo\n");
                            $feedSubmissionInfo = $submitFeedResult->getFeedSubmissionInfo();
                            if ($feedSubmissionInfo->isSetFeedSubmissionId()) 
                            {
                                echo("                    FeedSubmissionId\n");
                                echo("                        " . $feedSubmissionInfo->getFeedSubmissionId() . "\n");
                            }
                            if ($feedSubmissionInfo->isSetFeedType()) 
                            {
                                echo("                    FeedType\n");
                                echo("                        " . $feedSubmissionInfo->getFeedType() . "\n");
                            }
                            if ($feedSubmissionInfo->isSetSubmittedDate()) 
                            {
                                echo("                    SubmittedDate\n");
                                echo("                        " . $feedSubmissionInfo->getSubmittedDate()->format(DATE_FORMAT) . "\n");
                            }
                            if ($feedSubmissionInfo->isSetFeedProcessingStatus()) 
                            {
                                echo("                    FeedProcessingStatus\n");
                                echo("                        " . $feedSubmissionInfo->getFeedProcessingStatus() . "\n");
                            }
                            if ($feedSubmissionInfo->isSetStartedProcessingDate()) 
                            {
                                echo("                    StartedProcessingDate\n");
                                echo("                        " . $feedSubmissionInfo->getStartedProcessingDate()->format(DATE_FORMAT) . "\n");
                            }
                            if ($feedSubmissionInfo->isSetCompletedProcessingDate()) 
                            {
                                echo("                    CompletedProcessingDate\n");
                                echo("                        " . $feedSubmissionInfo->getCompletedProcessingDate()->format(DATE_FORMAT) . "\n");
                            }
                        } 
                    } 
                    if ($response->isSetResponseMetadata()) { 
                        echo("            ResponseMetadata\n");
                        $responseMetadata = $response->getResponseMetadata();
                        if ($responseMetadata->isSetRequestId()) 
                        {
                            echo("                RequestId\n");
                            echo("                    " . $responseMetadata->getRequestId() . "\n");
                        }
                    } 

                    echo("            ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");
         } catch (MarketplaceWebService_Exception $ex) {
             echo("Caught Exception: " . $ex->getMessage() . "\n");
             echo("Response Status Code: " . $ex->getStatusCode() . "\n");
             echo("Error Code: " . $ex->getErrorCode() . "\n");
             echo("Error Type: " . $ex->getErrorType() . "\n");
             echo("Request ID: " . $ex->getRequestId() . "\n");
             echo("XML: " . $ex->getXML() . "\n");
             echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
         }
     }

    function actionGet_by_pid($pid){
        $url = 'https://www.actneed.com/api/category/get?shop_id=4343&parent_id='.$pid;
        $ch = curl_init();
        // 设置你需要抓取的URL
        curl_setopt($ch, CURLOPT_URL, $url);
        // 设置header
        curl_setopt($ch, CURLOPT_HEADER, false);
        // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 运行cURL，请求网页
        $data = curl_exec($ch);
        // 关闭URL请求
        curl_close($ch);
        $result=json_decode($data,true);

       // var_dump($result);die;
        if($result['categories']) {
            $category_model = new Category();
            foreach ($result['categories'] as $key => $value) {
                $value['pid']=$pid;
                $value['firstcat_id']='660153';// changge base on different  times
                 $value['platform_id']='1';
                $category_model->saveCate($value);
                if($value['leaf']==0){
                 $this->actionGet_by_pid($value['id']);
                }

            }
        }


    }
    //从actneed上抓取分类,废弃
    function actionGet_cate(){
       //$result = $this->actionGet_by_pid(660153);//0 or 658095  or 660153

    }
	//输出变量模板接口，废弃
    function actionGet_variation_theme(){

      $specifics_model = new Specifics();
      $cat_id =Yii::$app->request->get('cat_id');
      $cat_data = $specifics_model->getSpecifics($cat_id);
      if($cat_data){
        $cat_data['status'] = true;
        return json_encode($cat_data);
      }else{
        return json_encode(["status"=>false,"msg"=>"no this categories"]);
      }
    }
    //输出商品类型接口，废弃
    function actionGet_product_type(){
      $productType_model = new ProductType();
      $cat_id =Yii::$app->request->get('cat_id');
      $subcat_id =Yii::$app->request->get('subcat_id');
      $cat_data = $productType_model->getProductType($cat_id,$subcat_id);
      if($cat_data){
        $cat_data['status'] = true;
        return json_encode($cat_data);
      }else{
        return json_encode(["status" => false,"msg" => "no this categories"]);
      }
    }




    // loop insert producttype infos to our databases
    function actionCopy_producttype(){
        //$category_model = new Category();
        $categorys= Category::find()
        ->where(['firstcat_id' => 658095,'leaf'=>1])
        ->asArray()
        ->all();
        //var_dump($categorys);die;
        if($categorys){
          foreach ($categorys as $key => $value) {
            $this->actionGet_producttype_by_catid($value['firstcat_id'],$value['cat_id']);
          }

        }


    }


     // get product type info   from actneed
    function actionGet_producttype_by_catid($cat_id,$child_id){

        $url = "https://www.actneed.com/amazon/4343/api/product_type";
        $ch = curl_init();
        // 设置你需要抓取的URL
        curl_setopt($ch, CURLOPT_URL, $url);
        // 设置header
        curl_setopt($ch, CURLOPT_HEADER, false);
        // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $post_data = [
          "root_id" => $cat_id,
          'child_id' => $child_id,//'660663',
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        // 运行cURL，请求网页
        $data = curl_exec($ch);
        // 关闭URL请求
        curl_close($ch);
        $result=json_decode($data,true);
       // var_dump($result);die;
        $producttype_model = new ProductType();
        $productType_data = [];
        $productType_data['site_id'] = 11;
        $productType_data['store_id'] = 11;
        $productType_data['cat_id'] = $cat_id;
        $productType_data['subcat_id'] = $child_id;

        if($result['status'] == true) {
          if($result['product_type'] && is_array($result['product_type'])){
            $productType_data['product_type'] = '';
            foreach ($result['product_type']  as $key => $value) {
              $productType_data['product_type'] .= $key==0 ? $value :','.$value;
            }
          }
          if($result['special_upc'] && is_array($result['special_upc'])){
            $productType_data['special_upc'] = '';
            foreach ($result['special_upc']  as $key => $value) {
              $productType_data['special_upc'] .= $key==0 ? $value :','.$value;
            }
          }
          $productType_data['other_value'] = $result['other_value'] ;
          $productType_data['item_type'] = $result['item_type'] ;
          $producttype_model->createProductType($productType_data);
        }
    }

    function actionCopy_specifics(){


      $categorys= Category::find()
        ->where(['level'=>1])
        ->asArray()
        ->all();
       // var_dump($categorys);die;
        if($categorys){
          foreach ($categorys as $key => $value) {
            $this->actionGet_variation_by_cat_id($value['cat_id']);
          }

        }

    }








}