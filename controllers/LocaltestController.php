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
use app\assets\AmazonBase;
use app\libraries\MyHelper;
use app\libraries\Snoopy;
use app\models\GoodsSyncOnline;
use app\models\GoodsSyncSku;
use app\models\Monitor;
use app\models\FollowsellerDetail;
use app\assets\amazon\src\MarketplaceWebService\Samples\AmazonCommon;
/*
* 发布 商（产）品 入口
* add by echo 2016-06-02
*/
Class LocaltestController extends BaseController
{

    //重写属性，默认是加载layouts\main.php   这里不加载
    public $layout = false;

    private $_status = ['draft' => 0, 'waiting' => 1, 'dealing' => 2, 'failed' => 3, 'success' => 4];

    public function __construct($id, $module)
    {
        parent::__construct($id, $module);

        $this->initProductManagement();
    }
    
    function actionGetupsuccessreturnvalue(){
        $newapi = new \app\assets\Amazon($seller_id = 'A3JT3LBRIKYRKF', $access_key= 'AKIAI777T77AT5ISTVPQ',$secret_access = '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');
        $result = $newapi->getUpSuccessReturnValue($marketplaceId = 'ATVPDKIKX0DER', $idType = 'ASIN', $idList = ['B01HT8XSPO','B01FE0PDCU','B01FE0PGL8','B019Q9FQAE','B01HS23PDQ','B01HROLXV6','B01HROM022','B01HS0G7LU','B01HS0G9MM','B01HEWDPRG','B01HPYAY9K','B01HPEA9NQ']);
        print_r($result);die;
    }
    function actionRequestreport(){
        $newapi = new \app\assets\Amazon($seller_id = 'A3JT3LBRIKYRKF',$access_key = 'AKIAI777T77AT5ISTVPQ',$secret_access = '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');
        $result = $newapi->requestReport($reportType = '_GET_MERCHANT_LISTINGS_DATA_');
    }
    function actionGetGeneratedreportId(){
        $newapi = new \app\assets\Amazon($seller_id = 'A3JT3LBRIKYRKF',$access_key = 'AKIAI777T77AT5ISTVPQ','iBAyVkAoyLpVnwO2KZNkm0yGiZD10SqJLfJLL5di');
        $result = $newapi->getReportRequestList();
        return $result;
        //var_dump($result);die;
    }
    function actionGetreport(){
        $generatedReportId =$this->actionGetGeneratedreportId();
        $newapi = new \app\assets\Amazon($seller_id = 'A3JT3LBRIKYRKF',$access_key = 'AKIAI777T77AT5ISTVPQ','iBAyVkAoyLpVnwO2KZNkm0yGiZD10SqJLfJLL5di');
        $result = $newapi->getReport($generatedReportId);
        var_dump($result);die;
    }

    //传参 goods_online_id  字符串或数组
    function actionChangeprice(){

        $pp = new GoodsSyncSku;
        var_dump($pp->attributes());die;
        $goods_online_id =12 ;//[1,2]
        $changeData = GoodsSyncSku::getSyncGoodsInfo($goods_online_id);
        $data_array = [];
        foreach ($changeData as $key => $value) {
            $data_array[] = [
                'Message' =>[
                        'MessageID' => $key+1,
                        'Price' =>[
                            'SKU' => $value['sku'],
                            'StandardPrice' => $value['price'],
                        ],
                ],
            ];
        }
        //arrayBuildXml方法 第一个参数MerchantIdentifier就是seller_id
        $xml = MyHelper::arrayBuildXml('A3JT3LBRIKYRKF', $data_array, $type = 'Price');
        $newapi = new \app\assets\Amazon($seller_id = 'A3JT3LBRIKYRKF',$access_key = 'AKIAI777T77AT5ISTVPQ','iBAyVkAoyLpVnwO2KZNkm0yGiZD10SqJLfJLL5di');
        $result = $newapi->changePrice($xml);
    }

    //传参goods_online_id  字符串或数组
    function actionChangesaleprice(){
        $goods_online_id =1 ;//[1,2]
        $changeData = GoodsSyncSku::getSyncGoodsInfo($goods_online_id);
        $data_array = [];
        foreach ($changeData as $key => $value) {
            $data_array[] = [
                'Message' =>[
                        'MessageID' => $key+1,
                        'Price' =>[
                            'SKU' => $value['sku'],
                            'StandardPrice' => $value['price'],
                            'Sale' => [
                                 'StartDate' => MyHelper::Time2Gtime($value['sales_begin_date']),
                                 'EndDate' => MyHelper::Time2Gtime($value['sales_end_date']),
                                 'SalePrice' => $value['sale_price']
                            ]
                        ],
                ],
            ];
        }
        //arrayBuildXml方法 第一个参数MerchantIdentifier就是seller_id
        $xml = MyHelper::arrayBuildXml('A3JT3LBRIKYRKF', $data_array, $type = 'Price');
        $newapi = new \app\assets\Amazon($seller_id = 'A3JT3LBRIKYRKF',$access_key = 'AKIAI777T77AT5ISTVPQ','iBAyVkAoyLpVnwO2KZNkm0yGiZD10SqJLfJLL5di');
        $result = $newapi->changePrice($xml);
    }


    //传参goods_online_id  字符串或数组
    function actionChangeinventory(){
        $goods_online_id =1 ;//[1,2]
        $changeData = GoodsSyncSku::getSyncGoodsInfo($goods_online_id);
        $data_array = [];
        foreach ($changeData as $key => $value) {
            $data_array[] = [
                'Message' =>[
                        'MessageID' => $key+1,
                        'OperationType' => 'Update',
                        'Inventory' =>[
                            'SKU' => $value['sku'],
                            'Quantity' => $value['stock'],
                        ],
                ],
            ];
        }
        //arrayBuildXml方法 第一个参数MerchantIdentifier就是seller_id
        $xml = MyHelper::arrayBuildXml('A3JT3LBRIKYRKF', $data_array, $type = 'Inventory');
        $newapi = new \app\assets\Amazon($seller_id = 'A3JT3LBRIKYRKF',$access_key = 'AKIAI777T77AT5ISTVPQ','iBAyVkAoyLpVnwO2KZNkm0yGiZD10SqJLfJLL5di');
        $result = $newapi->changeInventory($xml);
    }


    //传参goods_online_id
    function actionChangeproductinfo(){
        $goods_online_id =8 ;
        $infoData = GoodsSyncOnline::getSyncGoodsInfo($goods_online_id);
        $bullet_points = unserialize($infoData['bullet_points']);
        $search_terms = unserialize($infoData['keywords']);
        $data_array = [
            'Message' =>[
                    'MessageID' => 1,
                    'OperationType' => 'PartialUpdate',
                    'Product' =>[
                        'SKU' => $infoData['sku'],
                        'DescriptionData' => [
                            'Title' =>$infoData['title'],
                            'Description' => $infoData['description'],
                            'BulletPoint1' => isset($bullet_points[0]) ? $bullet_points[0] : ' ',
                            'BulletPoint2' => isset($bullet_points[1]) ? $bullet_points[1] : ' ',
                            'BulletPoint3' => isset($bullet_points[2]) ? $bullet_points[2] : ' ',
                            'BulletPoint4' => isset($bullet_points[3]) ? $bullet_points[3] : ' ',
                            'BulletPoint5' => isset($bullet_points[4]) ? $bullet_points[4] : ' ',
                            'SearchTerms1' => isset($search_terms[0]) ? $search_terms[0] : ' ',
                            'SearchTerms2' => isset($search_terms[1]) ? $search_terms[1] : ' ',
                            'SearchTerms3' => isset($search_terms[2]) ? $search_terms[2] : ' ',
                            'SearchTerms4' => isset($search_terms[3]) ? $search_terms[3] : ' ',
                            'SearchTerms5' => isset($search_terms[4]) ? $search_terms[4] : ' '
                        ]
                    ]
            ]
        
        ];
        //arrayBuildXml方法 第一个参数MerchantIdentifier就是seller_id
        $xml = MyHelper::arrayBuildXml('A3JT3LBRIKYRKF', $data_array, $type = 'Product');
        // var_dump($xml);die;
        $newapi = new \app\assets\Amazon($seller_id = 'A3JT3LBRIKYRKF',$access_key = 'AKIAI777T77AT5ISTVPQ','iBAyVkAoyLpVnwO2KZNkm0yGiZD10SqJLfJLL5di');
        $result = $newapi->changeProductInfo($xml);
    }


    function a()
    {
        while (true) {

            if (!$this->url) {
                break;
            }

            $this->init();

        }
    }

    function init()
    {
        $this->html = $this->getHtml($this->url);

        $this->getPageInfo();

        $this->insert($this->data);
    }

    function getHtml()
    {
        // ....
        $this->html = $html;
    }

    function actionTestgeturl(){
        $asin = "B002SVPAMW";//B000NJJ1MQ  B00WGDEON8  B002SVPAMW
        $url = "https://www.amazon.com/gp/offer-listing/" . $asin; 
        $pageInfo = $this->getPageInfo($url);
        while ( $pageInfo['nextUrl']) {
            $this->getPageInfo($pageInfo['nextUrl']);
        }

    }
    public function getPageInfo($url){
        require_once('/var/www/crossborder/libraries/phpquery-master/phpQuery/phpQuery.php');
        $snoopy = new Snoopy;
        $snoopy->fetch($url);           //获取所有内容
        $file = $snoopy->results;    //显示结果
        $file = file_get_contents('/var/www/crossborder/content.html');
        $content = \phpQuery::newDocumentHTML($file);

        $image = pq('#olpProductImage img')->attr('src');
        $title = trim(pq('#olpProductDetails h1')->text());
        $content_array = pq('div.a-row.a-spacing-mini.olpOffer');
        foreach ($content_array as $key => $value) {
            $sellerInfo[$key]['price'] = trim(pq($value)->find('.olpOfferPrice')->text(),' $');

            if( pq($value)->find('span')->hasClass('olpShippingPrice') ){
                $sellerInfo[$key]['shipFree'] = trim(pq($value)->find('.olpShippingPrice')->text(), ' $');
            } else {
                $sellerInfo[$key]['shipFree'] = 0;
            }

            if(pq($value)->find('.a-spacing-none.olpSellerName')->find('span')->hasClass('a-size-medium')){
                $sellerInfo[$key]['sellerName'] = pq($value)->find('.a-spacing-none.olpSellerName')->find('a')->text();
                $sellerInfo[$key]['sellerId'] = substr(pq($value)->find('.a-spacing-none.olpSellerName')->find('a')->attr('href'),strpos(pq($value)->find('.a-spacing-none.olpSellerName')->find('a')->attr('href'), 'seller=')+7);
            } else {
                $sellerInfo[$key]['sellerName'] = 'amazon';
                $sellerInfo[$key]['sellerId'] = 'amzon';
            }
        }
        if(pq('div.a-text-center.a-spacing-large')->text() !=''){
            if(!pq('li.a-selected')->next('li')->hasClass('a-last')){
                $nextUrl= 'https://www.amazon.com'.pq('li.a-selected')->next('li')->find('a')->attr('href');
            } else{
                $nextUrl = null;
                $this->url = null;
            }
        } else {
            $nextUrl = null;
        }
        Monitor::createMonitor($sellerInfo);
        return $nextUrl;
        /*if($nextUrl){
            $return = $this->getSellerInfo($nextUrl);
            $sellerInfos = array_merge($sellerInfo, $return);
        } else{
            $sellerInfos = $sellerInfo;
        }
        return $sellerInfos;*/
    }

}



?>