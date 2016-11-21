<?php 
namespace app\commands;
use Yii;
use yii\console\Controller;
use app\assets\amazonAPI\classes\helper;
use yii\helpers\Json;
use app\libraries\Snoopy;
use app\models\Monitor;
use app\models\FollowsellerDetail;
use app\service\SmsService;
use app\models\User;
use app\libraries\Log;

require_once('/var/www/crossborder/libraries/phpquery-master/phpQuery/phpQuery.php');
/*
* 发布 商（产）品 入口
* add by echo 2016-06-02
*/
Class FollowController extends Controller
{
    public $html = '';
    public $url ='';
    public $data = '';
    public $asin = '';
    public $urls = [];
    public function __construct($id, $module)
    {
        parent::__construct($id, $module);
    }

    function initOperateNew()
    {

        if (!$this->urls) {
            return false;
        }

        $sellerArray = [];
        foreach ($this->urls as $key => $url) {
            $html = $this->getHtml($url);
            if (!$html) {
                return false;
            }
            $sellerArray = array_merge($sellerArray, $this->getPageInfo($html));
        }

        return $sellerArray;
    }

    private function _saveLog($message)
    {
        $message = date('Y-m-d H:i:s') . "\t$message\n";
        //fwrite(STDOUT, $message);
        Log::save($message, 'follow_sell');
    }

    /*private function _getMainInfo($mainUrl)
    {
        $html = $this->getHtml($mainUrl);
        if (!$html) {
            return false;
        }

        // set 2
        $mainData = $this->_matchMainPage($html);
        if ($mainData['ifFollow']) {
            $this->_setPageUrls(ceil($mainData['seller_count']/10));
        }

        return $mainData;
    }*/

    private function _setPageUrls($total, $hasFirstPage = true)
    {
        $urlArray = [];
        if ($hasFirstPage) {
            $urlArray[1] = $firstUrl = 'https://www.amazon.com/gp/offer-listing/'. $this->asin;
        }
        for ($i=2; $i <= $total; $i++) {
            $urlArray[$i] = 'https://www.amazon.com/gp/offer-listing/' . $this->asin . '/ref=olp_page_'.$i.'?ie=UTF8&overridePriceSuppression=1&startIndex='.($i-1)*10;
        }
        $this->_setOption('urls', $urlArray);
    }

    private function _setOption($key, $value)
    {
        $this->$key = $value;
    }

    function actionSync(){//B002SVPAMW
        $this->_saveLog("process begin...");
        $monitorInfo = Monitor::getMonitorInfo();
        if (!$monitorInfo) {
            $this->_saveLog("no Data.end..\n");
            die;
        }
        foreach ($monitorInfo as $key => $monitor) {
            usleep(mt_rand(1000000, 2000000));
            $this->urls = [];//初始化 url
            $this->_setOption('asin', $monitor['asin']);
            $mainUrl = 'https://www.amazon.com/gp/offer-listing/'. $this->asin;
            $html = $this->getHtml($mainUrl);
            if (!$html) {
                $this->_saveLog("{$monitor['id']} {$monitor['asin']} catch fail.");
                continue;
            }
            $mainData = $this->getPageInfo($html ,true);
            $headInfo = $mainData['headInfo'];
            $this->_setPageUrls($headInfo['maxPage'], false);
            unset($mainData['headInfo']);
            $sellerArray = [];
            if ($headInfo['maxPage'] > 1) {
                if (!($sellerArray = $this->initOperateNew())) {
                    $this->_saveLog("{$monitor['id']} {$monitor['asin']} not seller.");
                    continue;
                }
            }
            $sellerArray = array_merge($mainData, $sellerArray);
            if( empty($sellerArray) ){
                $this->_saveLog("monitor_id: {$monitor['id']}无更卖信息.\n");
                continue;
            }
            $tmp = [];
            $fba = 0;
            foreach ($sellerArray as $k => $v) {
                $v['price'] = str_replace(',', '', $v['price']);
                if (isset($tmp[$v['sellerId']])) {
                    if ($v['price'] + $v['shipFree'] < $tmp[$v['sellerId']]['price'] + $tmp[$v['sellerId']]['shipFree']) {
                        if ($tmp[$v['sellerId']]['isFBA'] && !$v['isFBA']) {
                            $fba--;
                        }
                        $tmp[$v['sellerId']] = $v;
                    }
                    continue;
                }
                if ($v['isFBA']) {
                    $fba++;
                }
                $tmp[$v['sellerId']] = $v;
            }
            $headInfo['low_price'] = str_replace(',', '', $sellerArray[0]['price']) + $sellerArray[0]['shipFree'];
            $headInfo['seller_count'] = count($tmp);
            $headInfo['amazon_seller_count'] = count($sellerArray);
            $headInfo['fba_count'] = $fba;
            try{
                Monitor::saveMonitor($headInfo, $monitor['id']);
            } catch (\Exception $e){
                $this->_saveLog("monitor_id: {$monitor['id']} 修改更新状态出错 ".$e->getMessage()."\n");
            }
            // save detail in
            $is_send = FollowsellerDetail::recordSellerDetail($tmp, $monitor['id'], $headInfo['seller_count'], $headInfo['amazon_seller_count']);
            $this->_saveLog("monitor_id: {$monitor['id']} process finish\n");

            if($is_send){
                $mobile = User::findOne(['id'=>$monitor['user_id']])->mobile;
                $ret = SmsService::sendForMonitor($mobile, $monitor['asin']);
                if (!$ret['success']) {
                    $this->_saveLog("monitor_id: {$monitor['id']} send sms fail Msg:" . $ret['msg']);
                }
            }
        }
        $this->_saveLog("all process finish");
    }
    public function _matchMainPage($html)
    {
        $content = \phpQuery::newDocumentHTML($html);
        $title = trim(pq('span#productTitle')->text());
        $image = pq('div#imgTagWrapperId')->find('img')->attr('data-old-hires');
        if(pq('#merchant-info')->find('a:first')->text()){
            $buybox_seller = pq('#merchant-info')->find('a:first')->text();
        } else {
            $buybox_seller = 'amazon';
        }
        if(pq('#moreBuyingChoices_feature_div')->find('#mbc-action-panel-wrapper')->find('div.a-box')->hasClass('a-text-center')){
            $ifFollow = 1;
            $sellerString = trim(pq('.a-box-inner.a-padding-base')->find('a')->text());
            preg_match('/\d*/',$sellerString,$Count);
            $seller_count = $Count[0];
        } else {
            $ifFollow = 0;
            $seller_count = 0;
        }
        $mainData = [
          'title' => $title,
          'image' => $image,
          'buybox_seller' => $buybox_seller,
          'amazon_seller_count' => $seller_count,
          'ifFollow' => $ifFollow,
        ];
        return $mainData;
    }

    /*//跟卖信息抓取时，更新两个表
    public function insert(){
        $monitorId = Monitor::findMonitorIdByAsin($this->asin);
        FollowsellerDetail::recordSellerDetail($this->data,$monitorId);
    }*/

    public function getHtml($url) {
        usleep(mt_rand(100000, 300000));
        //return file_get_contents('/var/www/crossborder/content.html');
        $snoopy = new Snoopy;
        $snoopy->agent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/51.0.2704.79 Chrome/51.0.2704.79 Safari/537.36';
        $snoopy->read_timeout = 30;
        return $this->_execTimes($snoopy, $url);
    }

    private function _execTimes($snoopy, $url ,$times = 3)
    {
        $snoopy->fetch($url);
        if ($snoopy->error) {
            usleep(mt_rand(90000, 200000));
            $times--;
            if ($times > 0) {
                return $this->_execTimes($snoopy, $url, $times);
            }
        }
        return $snoopy->results;
    }

    public function getPageInfo($html, $head = false){
        $content = \phpQuery::newDocumentHTML($html);
        $content_array = pq('div.a-row.a-spacing-mini.olpOffer');
        $title = trim(pq('#olpProductDetails')->find('h1.a-size-large.a-spacing-none')->text());
        $image = pq('#olpProductImage')->find('img')->attr('src');
        
        $sellerInfo = [];
        if ($head) {
            $maxPage = 1;
            if ( pq('div.a-text-center.a-spacing-large')->text() != '' ) {
                $pageInfo = pq('div.a-text-center.a-spacing-large')->html();
                $pattern = '/<li[^>]*>.*?<a[^>]*>(\d+)<.+?/is';
                preg_match_all($pattern, $pageInfo, $matches);
                $maxPage = (int)max($matches[1]);
            }

            $headInfo = [
                    'title' => $title,
                    'image' => $image,
                    'maxPage' => $maxPage,
            ];
            $sellerInfo['headInfo'] = $headInfo;
        }
        foreach ($content_array as $key => $value) {
            if(pq($value)->find('.a-column.a-span2')->find('span')->hasClass('olpOfferPrice')){
                $sellerInfo[$key]['price'] = trim(pq($value)->find('.olpOfferPrice')->text(),' $');
            } else {
                $sellerInfo[$key]['price'] = 0;
            }

            if( pq($value)->find('span')->hasClass('olpShippingPrice') ){
                $sellerInfo[$key]['shipFree'] = trim(pq($value)->find('.olpShippingPrice')->text(), ' $');
            } else {
                $sellerInfo[$key]['shipFree'] = 0;
            }
            //sellerName sellerId
            if(pq($value)->find('.a-spacing-none.olpSellerName')->find('span')->hasClass('a-size-medium')){
                $sellerInfo[$key]['sellerName'] = pq($value)->find('.a-spacing-none.olpSellerName')->find('a')->text();
                $sellerInfo[$key]['sellerId'] = substr(pq($value)->find('.a-spacing-none.olpSellerName')->find('a')->attr('href'),strpos(pq($value)->find('.a-spacing-none.olpSellerName')->find('a')->attr('href'), 'seller=')+7);
            } else {
                $sellerInfo[$key]['sellerName'] = 'Amazon';
                $sellerInfo[$key]['sellerId'] = 'ATVPDKIKX0DER';
            }
            //if FBA
            if(pq($value)->find('span.supersaver')->find('i')->hasClass('a-icon-prime')){
                $sellerInfo[$key]['isFBA'] = 1;
            } else {
                $sellerInfo[$key]['isFBA'] = 0;
            }
        }
        /*if(pq('div.a-text-center.a-spacing-large')->text() !=''){
            if(!pq('li.a-selected')->next('li')->hasClass('a-last')){
                $nextUrl= 'https://www.amazon.com'.pq('li.a-selected')->next('li')->find('a')->attr('href');
            } else {
                $nextUrl = null;
                $this->url = null;
            }
        } else {
            $nextUrl = null;
        }*/
        \phpQuery::unloadDocuments();
        return $sellerInfo;
        //$this->data = ['sellerInfo'=>$sellerInfo];
        /* $this->data = ['sellerInfo'=>$sellerInfo, 'image'=>$image, 'title'=> $title, 'nextUrl' =>$nextUrl];*/
    }

    public function actionDemo(){
        
        $mobile = User::findOne(['id'=>1])->mobile;
        SmsService::sendForMonitor($mobile, "B00H7HSSJ4");
    }

}



?>