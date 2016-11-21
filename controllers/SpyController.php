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
use app\assets\amazon\src\MarketplaceWebService\Samples\AmazonCommon;
/*
* SPY 跟卖listing
* @SHB
*/
Class SpyController extends BaseController
{

    //重写属性，默认是加载layouts\main.php   这里不加载
    public $layout = false;

    public $market_website = [
            'A2EUQ1WTGCTBG2'=>'https://www.amazon.ca/',
            'ATVPDKIKX0DER'=>'https://www.amazon.com/',
            'A1AM78C64UM0Y8'=>'https://www.amazon.com.mx/',
            'A1PA6795UKMFR9'=>'https://www.amazon.de/',
            'A1RKKUPIHCS9HS'=>'https://www.amazon.es/',
            'A13V1IB3VIYZZH'=>'https://www.amazon.fr/',
            'A21TJRUUN4KGV'=>'https://www.amazon.in/',
            'APJ6JRA9NG5V4'=>'https://www.amazon.it/',
            'A1F83G8C2ARO7P'=>'https://www.amazon.co.uk/',
            'A1VC38T7YXB528'=>'https://www.amazon.co.jp/',
            'AAHKV2X7AFYLW'=>'https://www.amazon.cn/'
        ];

    public function __construct($id, $module)
    {
        parent::__construct($id, $module);

        $this->initProductManagement();
    }
    
    function actionIndex(){
        $url = "https://www.amazon.com/gp/offer-listing/B002SVPAMW";
        // $url = "https://www.51job.com";
        // $url = "https://cp-jp.cloud.z.com";
        $ch = curl_init();

        // curl_setopt($ch, CURLOPT_URL,$url);
        // curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        //socks5 使用qee13 作为sock5代理服务器转发请求，需要服务器上开启服务 service supervisord start
        // https://github.com/clowwindy/shadowsocks/wiki/%E7%94%A8-Supervisor-%E8%BF%90%E8%A1%8C-Shadowsocks
        // http://ydt619.blog.51cto.com/316163/1055334
        
        curl_setopt($ch, CURLOPT_PROXY, '133.130.96.188:3128');
        //curl_setopt($ch, CURLOPT_PROXYPORT, 80);
        curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, "haibeike:proxy_haibeike");
        // curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
            
        //data
        //curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        //curl_setopt($ch, CURLOPT_HEADER, true);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/51.0.2704.79 Chrome/51.0.2704.79 Safari/537.36');
        
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            die(curl_error($ch));
        }
        curl_close($ch);
        die($response);
        die(123);
    }


}



?>