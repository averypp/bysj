<?php 


namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Product;
use app\models\GoodsInfo;
use app\models\Store;
use app\models\AmazonTemplate;
use app\models\AmazonBtg;
use app\models\AmazonFeeds;
use app\models\SeaShellResult;
use yii\data\Pagination;
use app\assets\amazonAPI\classes\helper;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use app\assets\AmazonBase;
use app\libraries\MyHelper;
use app\assets\amazon\src\MarketplaceWebService\Samples\AmazonCommon;
/*
* 发布 商（产）品 入口
* add by echo 2016-06-02
*/
Class PublicProductController extends BaseController
{

    //重写属性，默认是加载layouts\main.php   这里不加载
    public $layout = false;

    private $_status = ['draft' => 0, 'waiting' => 1, 'dealing' => 2, 'failed' => 3, 'success' => 4];

    public function __construct($id, $module)
    {
        parent::__construct($id, $module);

        $this->initProductManagement();
    }

    public function actionIndex($status)
    {

        if (!array_key_exists($status, $this->_status)) {
            $this->redirect('/', 301);
        }
        
        $params = [
            'status' => $this->_status[$status],
            'page_no' => $this->_request->get('page_no'),
        ];

        $productModel = new Product();
        $data = $productModel->getShopGoodsList($this->_shopId, $params);
        $data['totalCount'] = $productModel->getStatusCount($this->_shopId);
        $data['status'] = $status;
        $data['categories'] = AmazonBtg::getCategoryByShopId($this->_shopId);
        $data['requestUri'] = $this->getRequestUri('page_no');
        $data['shopInfo'] = $this->_shopInfo;
        $data['BRcount'] = $this->_BRcount;
        return $this->render('list', $data);
    }

    public function actionShopChange()
    {
        $storeMedol = new Store();
        $re = $storeMedol->getStores($this->_userId, false);

        $data = ['shops' => []];
        foreach ($re as $k => $v) {
            if ($v['id'] == $this->_shopId) {
                continue;
            }
            $one = [
                'platform' => $v['platform']['platform_name'],
                'shop_id' => $v['id'],
                'site_name' => $v['site']['platform_name'],
                'name' => $v['store_name'],
            ];

            $data['shops'][] = $one;
        }
        $data['status'] = 1;

        return $this->returnJsonData($data);
    }

    public function actionSearch()
    {   

        $feeds = [];
        if (! ($condition = $this->_getConditionFeild()) ) {
            return $this->returnJsonData(true, '共搜索到0件商品', ['feeds' => $feeds]);
        }

        $params = [
            'status' => $this->_status[$condition['status']],
            'page_no' => 201,
        ];

        // PID Title SKU
        switch ($condition['option']) {
            case 'PID':
                $params['product_id'] = $condition['content'];
                break;
            case 'Title':
                $params['goods_name'] = $condition['content'];
                break;
            case 'SKU':
                $params['sku'] = $condition['content'];
                break;
            default:
                $params['goods_name'] = $condition['content'];
                break;
        }

        $productModel = new Product();
        $products = $productModel->getShopGoodsList($this->_shopId, $params)['products'];

        foreach ($products as $product) {
            $one = [
                'ErrorMessage' => null,
                'GalleryURL' => $product['mainImages'],
                'Id' => $product['id'],
                'Link' => 'javascript: void(0);',
                'Quantity' => 0,
                'SoldFlag' => '',
                'StartPrice' => $product['list_price'],
                'Style' => '#5bc0de',
                'Text' => '等待上传',
                'Title' => $product['item_name'],
                'UpdateTime' => $product['gmt_modified'],
            ];
            $feeds[] = $one;
        }
        
        return $this->returnJsonData(true, '共搜索到' . count($feeds) . '件商品', ['feeds' => $feeds]);
    }

    public function actionGroupSelect()
    {

        $data['status'] = 1;
        $data['group_list'] = [];
        return $this->returnJsonData($data);
    }

    public function actionNoCategory()
    {

        $data['status'] = 1;
        $data['result'] = [
            ['total' => 1, 'Category.ID' => 0, 'Category.Name' => ["未设置分类"]],
        ];

        return $this->returnJsonData($data);
    }


    public function actionSetCategory()
    {

        return $this->returnJsonData(['status' => 1, 'n' => 1]);
    }

    public function actionDelete()
    {

        if (! ($condition = $this->_getConditionFeild()) ) {
            return $this->returnJsonData(['status' => 0], '参数错误');
        }

        $productModel = new Product();
        $ret = $productModel->softDeletePorduct($condition['Ids'], $this->_shopId, $this->_status[$condition['status']]);
        if (!$ret) {
            return $this->returnJsonData(['status' => 0], '批量删除失败');
        }

        $msg = "共删除了" . count($condition['Ids']) . "件产品 成功:" . count($ret['succIds']) . "件 失败:" . count($ret['failIds']) . "件";

        return $this->returnJsonData(['status' => 1], $msg);
    }

    public function actionUpload()
    {

        if (! ($condition = $this->_getConditionFeild()) ) {
            return $this->returnJsonData(['status' => 0], '参数错误');
        }

        $productModel = new Product();
        $ret = $productModel->changeDealingStatus($condition['Ids'], 'upload', $this->_shopId);
        if (!$ret) {
            return $this->returnJsonData(['status' => 0], '上传失败');
        }

        $data = [
            'success_pid' => array_values($ret['succIds']),
            'total' => count($condition['Ids']),
            'upc_error' => [],
            'error_pid' => array_values($ret['failIds']),
        ];

        return $this->returnJsonData(['status' => 1], '', $data);
    }

    public function actionTranslate()
    {

        return $this->returnJsonData(['status' => 1], '翻译请求已提交');
    }

    public function actionCheck()
    {

        if (! ($condition = $this->_getConditionFeild()) ) {
            return $this->returnJsonData(['status' => 0], '参数错误');
        }

        $gmt = date('Y-m-d H:i:s');

        $feedsModel = new Product();
        $errors = $feedsModel->checkItems($condition['Ids']);
        $error_pid = $errors ? array_keys($errors) : [];

        return $this->returnJsonData(['status' => 1], '', compact('error_pid'));
    }

    private function _getConditionFeild()
    {
        $condition = $this->_request->post('condition');
        if (!$condition || !($condition = Json::decode($condition))) {
            return false;
        }

        return $condition;
    }

    
    function actionBd(){
        $this->actionCreateTplData(47);


    }
    function actionCreateTplData($good_id){
        $goodsinfo_model = new GoodsInfo();
        $amazonTemplate_model = new AmazonTemplate();
        $amazonBtg_model = new AmazonBtg();
        $goodsInfo = $goodsinfo_model->getProductInfoByIdForTpl($good_id);
        $tpl_id=$amazonBtg_model->getTplidByItemtype($goodsInfo[0]['item_type']);//更具item-type查询tplid 在byg表里  item-type就是keyword字段
        $tplInfo = $goodsinfo_model->getTplById($tpl_id);//模板数据去循环

        $tplName = $amazonTemplate_model->getTemplateById($tpl_id);//模板名称和版本
        $feed = new \app\assets\amazonAPI\classes\helper\WPLA_FeedDataBuilder();
        $feedData = $feed->buildNewProductsFeedData($goodsInfo ,$tplInfo ,$tplName);
        $customer = AmazonFeeds::findOne(['good_id' => $good_id]);
        if($customer){
            $customer->date_created = date('Y-m-d H:i:s');
            $customer->good_id = $good_id;
            $customer->FeedType = '_POST_FLAT_FILE_LISTINGS_DATA_';
            $customer->data = $feedData;
            $customer->save();
        } else {
            $amazonFeeds_model = new AmazonFeeds();
            $amazonFeeds_model->date_created = date('Y-m-d H:i:s');
            $amazonFeeds_model->good_id = $good_id;
            $amazonFeeds_model->FeedType = '_POST_FLAT_FILE_LISTINGS_DATA_';
            $amazonFeeds_model->data =  $feedData;
            $amazonFeeds_model->save();
        }
        
    }
    //  商品发布到亚马逊
    function actionPubtoamazon(){
        $good_id = Yii::$app->request->get('good_id'); // 产品id
        $store_id = Yii::$app->request->get('shopId'); // 产品id
        $feeds_info = AmazonFeeds::getFeedsByGoodId($good_id);
        $store_info = Store::getInfoById($store_id);
        $newapi = new \app\assets\Amazon($store_info['merchant_id'],$store_info['accesskey_id'], $store_info['secret_key']);
        //$newapi->demo();
        $result = $newapi->pubToAmazon($feeds_info['data']);
        //var_dump($result['FeedSubmissionId']);die;
        $customer = AmazonFeeds::findOne(['good_id' => $good_id]);
        $customer->FeedSubmissionId = $result['FeedSubmissionId'];
        $customer->FeedType = $result['FeedType'];
        $customer->SubmittedDate = $this->changeTime($result['SubmittedDate']);
        $customer->FeedProcessingStatus = $result['FeedProcessingStatus'];
        $customer->save();
    }
    public function changeTime($returnTime)
    {   
        // $returnTime = "2016-06-27T08:23:54Z";
        $d1=substr($returnTime,17,2); //秒
        $d2=substr($returnTime,14,2); //分
        $d3=substr($returnTime,11,2); // 时
        $d4=substr($returnTime,8,2); //日
        $d5=substr($returnTime,5,2); //月
        $d6=substr($returnTime,0,4); //年
        return $d6.'-'.$d5.'-'.$d4.' '.$d3.':'.$d2.':'.$d1;
    }


    //
    function actionGetSubmissionResult(){
        $good_id = Yii::$app->request->get('good_id'); // 产品id
        $store_id = Yii::$app->request->get('shopId'); // 产品id
        $submission_id = Yii::$app->request->get('submission_id'); // 产品id
        $store_info = Store::getInfoById($store_id);
       /* $reportInfo = $this->actionGetSubmissionReport($store_info['merchant_id'],$store_info['accesskey_id'], $store_info['secret_key'], $submission_id);*/
        $amazon = new AmazonBase();
        $amazon ->setAttibutes('SellerID', $store_info['merchant_id']);
        $amazon ->setAttibutes('AccessKeyID', $store_info['accesskey_id']);
        $amazon ->setAttibutes('SecretKey', $store_info['secret_key']);

        //接口的服务和版本号，不同的接口是不一样的，参与签名
        $service_and_version = '';
        try {//51527016977  good    51461016974  error 
            $reportInfo = $amazon->requestErrorReturn('GetFeedSubmissionResult', ['FeedSubmissionId'=>$submission_id], $service_and_version);
        } catch (Exception $e) {
            echo $e->getMessage();exit;
        }
        // var_dump($reportInfo);die;
        $errorInfo = $amazon->getReturnErrorMsg($reportInfo);
        preg_match_all('/\d{1,}/', $reportInfo, $matches);
        if($matches[0][0] == $matches[0][1] && $matches[0][1]>=1){
            $customer = AmazonFeeds::findOne(['good_id' => $good_id]);
            $customer->success = 'success';
            $customer->status = 'processed';
            $customer->save();
        } else {
            $customer = AmazonFeeds::findOne(['good_id' => $good_id]);
            $customer->results = implode(" ", $errorInfo);
            $customer->success = 'error';
            $customer->status = 'processed';
            $customer->save();
        }
        
    }

    function actionGetSubmissionReport( $merchant_id, $accesskey_id, $secret_key, $feedSubmissionId){
        $amazon = new AmazonBase();
        $amazon ->setAttibutes('SellerID', $merchant_id);
        $amazon ->setAttibutes('AccessKeyID', $accesskey_id);
        $amazon ->setAttibutes('SecretKey', $secret_key);

        //接口的服务和版本号，不同的接口是不一样的，参与签名
        $service_and_version = '';
        try {//51527016977  good    51461016974  error 
            $result = $amazon->requestErrorReturn('GetFeedSubmissionResult', ['FeedSubmissionId'=>$feedSubmissionId], $service_and_version);
            $errorReport = $result;
            /*if($result['Message']['ProcessingReport']['ProcessingSummary']['MessagesWithError'] == 0){

            } else {
                foreach ($result['Message']['ProcessingReport']['Result'] as $key => $value) {
                    $errorReport .= $value['ResultDescription']."\n";
                }
            }*/

            return $errorReport;
        } catch (Exception $e) {
            return $this->returnJsonData(false, $e->getMessage());
        }


    }

}



?>