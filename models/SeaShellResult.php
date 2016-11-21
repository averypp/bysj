<?php

namespace app\models;
use yii\helpers\Json;
use Yii;


/*
* 海贝壳项目 统一返回接口
* 主要包含
* {
*      "success":true,//接口请求是否成功
*      "message":"",//接口请求失败时的 提示信息 （请求成功可为空）
*      "content":Array[1]// 接口请求成功时的 数组数据（请求失败为空）
*  }
*
*/
class SeaShellResult{

    public function error($message){
        if(empty($message)){
            $message = "message param is miss";
        }
        $errorResponse = array("success" => false,"message" =>$message,"content"=>"");
        return Json::encode($errorResponse);
    }

    public function success($content){
        $errorResponse = array("success" => true,"message" =>"操作成功","content"=>$content);
        return Json::encode($errorResponse);
    }

    public function arrayToJson($array){
        $errorResponse = array("success" => true,"message" =>"","content"=>$array);
        return Json::encode($errorResponse);
    }

}


?>