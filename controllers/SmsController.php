<?php 

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\assets\yunpian\lib;
use app\models\SeaShellResult;
use app\service\SmsService;

class SmsController extends Controller{
	
	public function actionEcho(){
        $mobile = "18314870191";
        
        $smsService = new SmsService();
        $result = $smsService->send($mobile);
        print_r($result);
    }
    public function actionEcho2(){
        $mobile = "18314870191";
        // $dataTime = "2016-07-14";
        $asin = "B00WGDEON8,B00WGDEON8,B00WGDEON8,B00WGDEON8,B00WGDEON8,";
        $smsService = new SmsService();
        $result = $smsService->sendForMonitor($mobile,$asin);
        print_r($result);
    }

    public function actionGet(){
    	$smsService = new SmsService();
    	$smsService->getTpl();
    }

    public function actionCreatetpl(){
    	$smsService = new SmsService();
    	$smsService->createTpl2();
    }
}



?>