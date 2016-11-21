<?php

namespace app\service;
include_once(__DIR__ . '/../assets/yunpian/YunpianAutoload.php');
use Yii;
use app\assets\yunpian\lib\SmsOperator;
use app\assets\yunpian\lib\TplOperator;
use app\models\SeaShellResult;
class SmsService{

	public $_tplId = array("code"=>1473359,"asin"=>1473347);


	/**
	* [业务发送接口] 默认为 发送短信验证码
	* @param mobile:手机号 
	*
	*/
	public function send($mobile){

		$data['mobile'] = $mobile;
		$captcha = rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
		$content = "【出海宝】您的验证码是".$captcha;
		$data['text'] = $content;
		$result = $this->single_send($data);
    	if($result->success == true && $result->responseData['code'] == 0){
    		$data['code'] = $captcha;
    		$data['success'] = true;
    	}else{
    		$data['msg'] = $result->responseData['msg'].$result->responseData['detail'];
    		$data['success'] = false;
    	}
    	return $data;
	}

	/**
	* [商品跟卖监控 短信提醒接口] 
	* @param mobile:手机号 dateTime:"2016-07-14" asin:B00WGDEON8
	*
	*/
	public function sendForMonitor($mobile,$asin,$dateTime=""){
		

		$data['mobile'] = $mobile;
		if(empty($dateTime)){
			$dateTime = date("Y-m-d H:i:s");
		}
		$content = "【出海宝】您好，出海宝于" . $dateTime . "检测到您的商品被跟卖,请及时登陆出海宝查看详情。ASIN:" . $asin;
		$data['text'] = $content;
		$result = self::single_send($data);
    	if($result->success == true && $result->responseData['code'] == 0){
    		$data['success'] = true;
    	}else{
    		$data['msg'] = $result->responseData['msg'].$result->responseData['detail'];
    		$data['success'] = false;
    	}
    	return $data;
	}

	/**
	* [差评监控 短信提醒接口] 
	* @param mobile:手机号 dateTime:"2016-07-14" BRcount:最新中差评数量
	*
	*/
	public function sendForBadReview($mobile, $BRcount, $dateTime=""){

		$data['mobile'] = $mobile;
		if(empty($dateTime)){
			$dateTime = date("Y-m-d H:i:s");
		}
		$content = "【出海宝】您好，出海宝于" . $dateTime . "检测到您的商品有" . $BRcount . "条最新的中差评,请及时登陆出海宝查看详情。";
		$data['text'] = $content;
		$result = self::single_send($data);
    	if($result->success == true && $result->responseData['code'] == 0){
    		$data['success'] = true;
    	}else{
    		$data['msg'] = $result->responseData['msg'].$result->responseData['detail'];
    		$data['success'] = false;
    	}
    	return $data;
	}


	/**
	* [发送接口]
	* @param mobile: 手机号 content: 短信内容
	* @return array() 包含手机号/内容
	* 
	*/
	private function single_send($data){
		$smsOperator = new SmsOperator();
		return $smsOperator->single_send($data);
	}

	//获取自定义短信模板
	public function getTpl(){
		$tplOperator = new TplOperator();
		$result = $tplOperator->get();
		print_r($result);
	}

	public function createTpl(){
		$tplOperator = new TplOperator();
		$result = $tplOperator->add(array("tpl_id"=>'1473347',"tpl_content"=>'【出海宝】您好，#company#于#date#检测到您的商品被跟卖,请及时登陆出海宝查看详情。ASIN:#asin#'));
		print_r($result);
	}

	public function createTpl2(){
		$tplOperator = new TplOperator();
		$result = $tplOperator->add(array("tpl_id"=>'1473359',"tpl_content"=>'【出海宝】您的验证码是#code#'));
		print_r($result);
	}

	public function createTpl3(){
		$tplOperator = new TplOperator();
		$result = $tplOperator->add(array("tpl_id"=>'1484365',"tpl_content"=>'【出海宝】您好，出海宝于#date#检测到您的商品有#count#条最新的中差评,请及时登陆出海宝查看详情。'));
		print_r($result);
	}



}