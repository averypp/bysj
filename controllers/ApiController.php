<?php

namespace app\controllers;

use Yii;
use yii\helpers\Json;
use app\models\Store;
use app\libraries\Log;
use app\models\AmazonBtg;
use app\models\AmazonFeedTplData;
use app\models\AmazonFeedValues;
use app\models\AmazonTemplate;
use yii\web\Controller;
use yii\web\NotAcceptableHttpException;
class ApiController extends BaseController
{

    /**
     * 私钥
     */
    const ACCESS_TOKEN = 'C1B15C586D#d652C2648~k&1AD1F10~B';

    /**
     * 接受请求的数据（不包含signature,timestamp,nonce）
     * @var array
     */
    private $params = [];

    /**
     * 请求方法（get，post等等）,目前只支持get和post
     * @var string
     */
    private $requestMethod;

    /**
     * 签名
     * @var string
     */
    private $signature;

    /**
     * 请求参数时间戳
     * @var int
     */
    private $timestamp;

    /**
     * 签名用到的随机参数
     * @var string
     */
    private $nonce;

    /**
     * 过期时间，同一请求默认与服务器时间相差两分钟则失效
     * @var integer
     */
    private $expiredTime = 1200;

    /**
     * 必填字段
     * @var array
     */
    private $requireFields = ['signature', 'timestamp', 'nonce'];

    /**
     * 不需要验证签名的方法
     * @var array
     */
    private $noVerifyMethod = ['category-get'];

    /**
     * 重写父类方法，初始化一些数据
     * @param  object $action
     * @return boolean
     */
    public function beforeAction($action)
    {
        // 设置参数
        $this->setParams();

        if ($this->isCheckSignatrue()) {
            // 请求过期（2分钟内）
            $diffTime = abs(time() - $this->timestamp);
            if ($diffTime >= $this->expiredTime) {
                $this->response('本次请求已失效', -1);
            }

            // 验证签名失败
            if (!$this->checkSignature()) {
                $this->response('签名验证失败', -2);
            }
        }
        
        return parent::beforeAction($action);
    }

    /**
     * 是否需要验证签名
     * @return boolean
     */
    protected function isCheckSignatrue()
    {
        return !in_array($this->action->id, $this->noVerifyMethod);
    }

    /**
     * 初始化请求才参数以及检测必传字段signature,timestamp,nonce
     * 参数及请求不合法则抛出异常
     * @throws NotAcceptableHttpException 抛出异常错误消息
     */
    protected function setParams()
    {
        $this->requestMethod = Yii::$app->request->getMethod();
        if ($this->requestMethod == 'GET') {
            $params = Yii::$app->request->get();
        } elseif ($this->requestMethod == 'POST') {
            $params = Yii::$app->request->post();
        } else {
            throw new NotAcceptableHttpException("The requested {$this->requestMethod} does not Acceptable.");
        }

        if ($this->isCheckSignatrue()) {
            foreach ($this->requireFields as $field) {
                if (!isset($params[$field])) {
                    throw new NotAcceptableHttpException("The default parameter {$field} must be filled in");
                }
                $this->$field = $params[$field];
                unset($params[$field]);
            }
        }

        $this->params = $params;
    }

    /**
     * 验证签名
     * @return boolean 成功返回true，失败返回false
     */
    protected function checkSignature()
    {
        $tmpArr = array(self::ACCESS_TOKEN, $this->timestamp, $this->nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr != $this->signature) {
            return false;
        }

        return true;
    }

    /**
     * 响应数据
     * @param  string  $message [description]
     * @param  integer $code    状态码,失败状态码非0,成功等于0（默认是成功操作成功消息状态码）
     * @return json
     */
    protected function response($message, $code = 0)
    {
        die(json_encode(['code' => $code, 'message' => $message]));
    }

    public function actionCategoryGet()
    {

        $shopId = isset($this->params['shop_id']) ? intval($this->params['shop_id']) : 0;
        $parentId = isset($this->params['parent_id']) ? intval($this->params['parent_id']) : 0;

        $categories = [];

        $ret = AmazonBtg::getCategoryByShopId($shopId, $parentId);
        foreach ($ret as $key => $val) {
            $one = [
                'id' => $val['node_id'],
                'leaf' => $val['leaf'],
                'level' => $val['level'],
                'name' => $val['node_name'],
                'pin' => '',
                'query' => '',
                'tag' => '',
            ];
            $categories[] = $one;
        }

        return Json::encode(compact('categories'));

    }

    public function actionInsert(){
        $getData = Yii::$app->request->post('data');
        $getData = json_decode($getData, true);;
        $templateData = AmazonTemplate::getData();
        if($templateData){
            $nameArray = [];
            foreach ($templateData as $key => $value) {
                $nameArray[] = $value['site_id'].'-'.$value['name'];
            }
            if(in_array($getData['name'], $nameArray)){
                //return json_encode(['code' => 1, 'message' => $getData['name'] .' already exist']);
                $siteId = explode('-', $getData['name'])[0];
                $name = explode('-', $getData['name'])[1];
                $id = AmazonTemplate::findIdByNameAndSiteId($siteId, $name);
                //修改原表记录，不删除
                $updateReturn = AmazonTemplate::insertData($getData['templateData'], $id);
                if(!$updateReturn){
                    return json_encode(['code' => 0, 'message' => $getData['name'] .'update  template table error']);
                }
                $this->delete3table($siteId, $id);
                $this->insert3table($getData, $id);
                return json_encode(['code' => 1, 'message' => $getData['name'] .' update success']);
            } else {
                $newTplId = AmazonTemplate::insertData($getData['templateData']);
                if(!$newTplId){
                    return json_encode(['code' => 0, 'message' => $getData['name'] .'insert template table error']);
                }
                $this->insert3table($getData, $newTplId);
                return json_encode(['code' => 1, 'message' => $getData['name'] .' transmit success']);
            }
        } else { //数据库没有值，直接插入
            $newTplId = AmazonTemplate::insertData($getData['templateData']);
            if(!$newTplId){
                return json_encode(['code' => 0, 'message' => $getData['name'] .'first insert error']);
            }
            $this->insert3table($getData, $newTplId);
            return json_encode(['code' => 1, 'message' => $getData['name'] .' first transmit success']);
        }

    }
    function delete3table($siteId, $tplId){
        AmazonBtg::deleteData($siteId, $tplId);
        AmazonFeedTplData::deleteData($siteId, $tplId);
        AmazonFeedValues::deleteData($siteId, $tplId);
    }
    function insert3table($getData, $newTplId){
        foreach ($getData['btgDatas'] as $key => $value) {
            if(!AmazonBtg::insertData($value, $newTplId)){
                continue;
            }
        }
        foreach ($getData['FeedTplDatas'] as $key => $value) {
            if(!AmazonFeedTplData::insertData($value, $newTplId)){
                continue;
            }
        }
        foreach ($getData['FeedValuesDatas'] as $key => $value) {
            if(!AmazonFeedValues::insertData($value, $newTplId)){
                continue;
            }
        }
    }
}