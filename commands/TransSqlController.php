<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use app\libraries\MyHelper;
use app\models\AmazonBtgNew;
use app\models\AmazonFeedTplDataNew;
use app\models\AmazonFeedValuesNew;
use app\models\AmazonTemplateNew;

class TransSqlController extends Controller
{
    public function actionIndex(){
        $templateDatas = AmazonTemplateNew::getAllData();
        if(!$templateDatas){
            die('no datas');
        }
        $tmp = [];
        $ACCESS_TOKEN = 'C1B15C586D#d652C2648~k&1AD1F10~B';
        $time = time();
        $nonce = mt_rand(0, 99999);
        foreach ($templateDatas as $key => $value) {
            sleep(2);
            $btgDatas = AmazonBtgNew::getData($value['site_id'], $value['id']);
            $FeedTplDatas = AmazonFeedTplDataNew::getData($value['site_id'], $value['id']);
            $FeedValuesDatas = AmazonFeedValuesNew::getData($value['site_id'], $value['id']);
            $tmp['name'] = $value['site_id'].'-'.$value['name'];
            $tmp['templateData'] = $value;
            $tmp['btgDatas'] = $btgDatas;
            $tmp['FeedTplDatas'] = $FeedTplDatas;
            $tmp['FeedValuesDatas'] = $FeedValuesDatas;
            $datas = array('data'=>json_encode($tmp), 'signature'=>$this->checkSignature($ACCESS_TOKEN, $time, $nonce), 'timestamp'=>$time, 'nonce'=>$nonce);
            $return = MyHelper::request('http://crossborder.com/?r=api/insert', array('Expect:'), $datas);
            //$return = MyHelper::request('http://121.41.29.14/?r=api/insert', array('Expect:'), $datas);
            $return = json_decode($return, true);
            var_dump($return);
            if($return['code'] == 0){
                continue;
            }
            if($return['code'] == -1 || $return['code'] == -2){
                die('auth fail');
            }
            //die;
            
        }
        die('done!');
    }

    /**
     * 验证签名
     * @return
     */
    protected function checkSignature($ACCESS_TOKEN, $time, $nonce)
    {
        $tmpArr = array($ACCESS_TOKEN,$time, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        return $tmpStr;
    }

}
