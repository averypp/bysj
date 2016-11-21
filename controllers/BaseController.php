<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\data\Pagination;
use yii\helpers\Json;
use app\models\Store;
use yii\web\NotFoundHttpException;
use app\models\BadReviewMonitor;

class BaseController extends Controller
{

    public $enableCsrfValidation = false;
    protected $_request;
    protected $_userId;
    protected $_shopId;
    protected $_shopInfo;
    protected $_BRcount;

    public function __construct($id, $module)
    {
        parent::__construct($id, $module);

        $this->_request = Yii::$app->request;

        $this->_userId = Yii::$app->session->get('user_id');

        $BRMoniters = BadReviewMonitor::find()->where(['user_id'=>$this->_userId, 'is_read'=>0])->all();
        $BRcount=0;
        if(count($BRMoniters)){
            foreach($BRMoniters as $BRMoniter){
                $BRcount += $BRMoniter['review_total'];
            }
        }
        $this->_BRcount = $BRcount ? : 0;

        if (Yii::$app->user->isGuest) {
            return $this->redirect('/?r=site/login', 302);
        }

    }

    protected function initProductManagement()
    {

        $this->_shopId = abs(intval($this->_request->get('shopId')));

        if (!$this->_shopId) {
            throw new NotFoundHttpException();
        }

        Yii::$app->params['shopId'] = $this->_shopId;

        if (!Store::storeHasExists($this->_shopId, $this->_userId)) {
            $this->redirect('/', 301);
        }

        $info = Store::getStoreInfo($this->_shopId);
        $this->_shopInfo = [
            'platformName' => $info->platform->platform_name,
            'siteName' => $info->site->platform_name,
            'name' => $info->store_name,
        ];

    }

    protected function returnJsonData($succ, $msg = null, array $custArr = array())
    {

        $data = [];
        if (is_array($succ)) {
            $data = $succ;
        } else {
            $succ = (bool) $succ;
            $data = [
                'success' => $succ,
            ];
        }

        if ($msg) {
            $data['message'] = $msg;
        }

        if ($custArr) {
            $data = array_merge($data, $custArr);
        }

        return Json::encode($data);
    }

    protected function getRequestUri($filterField = null)
    {

        $requestUri = $_SERVER['REQUEST_URI'];

        if ($filterField !== null) {
            $parseArr = parse_url($requestUri);
            parse_str($parseArr['query'], $queryArr);
            if (isset($queryArr[$filterField])) {
                unset($queryArr[$filterField]);
            }
            $query = http_build_query($queryArr);
            $requestUri = $parseArr['path'] . '?' . $query;
        }

        return $requestUri;
    }

}