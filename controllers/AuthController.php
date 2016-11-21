<?php

namespace app\controllers;

use Yii;
use app\models\Platform;
use app\models\Store;
use app\assets\AmazonBase;
use yii\web\NotFoundHttpException;

class AuthController extends BaseController
{

    public function actionSite()
    {
        $request = Yii::$app->request;

        if ($request->isAjax) {

            $platform = $request->post('platform');
            $platformModel = new Platform();
            $siteInfo = $platformModel->getSiteByPlatform($platform);

            return $this->returnJsonData(true, '', ['sites' => $siteInfo]);
        }

    }

    public function actionSend()
    {

        $request = Yii::$app->request;

        $na = trim($request->get('na'));
        $sp = trim($request->get('sp'));
        $pa = trim($request->get('pa'));

        if (!$na || !$sp || !$pa) {
            throw new NotFoundHttpException();
        }

        $platformModel = new Platform();
        if (!($platform = $platformModel->isValidPlatformSite($pa, $sp))) {
            throw new NotFoundHttpException();
        }

        if (!$platform->site_url) {
            throw new NotFoundHttpException();
        }

        // other deal....

        return $this->redirect($platform->site_url, 301);
    }

    public function actionListen()
    {
        return $this->returnJsonData(false, '授权请求已过期');
    }

    public function actionToken()
    {

        $request = Yii::$app->request;

        $storeName = $request->post('na');
        $sessionId = $request->post('session_id');
        $platform = $request->post('platform');
        $siteId = $request->post('sp');
        $secretStr = $request->post('seller_id');

        list($sellerID, $accessKeyID, $secretKey) = explode('-', $secretStr);

        $platformId = Platform::getPlatformIdByName($platform);

        // Deduplication
        $where = [
            'user_id' => $this->_userId,
            'site_id' => $siteId,
            'platform_id' => $platformId,
            'is_deleted' => 'N',
        ];
        if (Store::find()->where($where)->exists()) {
            return $this->returnJsonData(false, '您已经拥有该店铺的管理权限,不能重复授权同一个店铺!');
        }
        if (Store::find()->where(['merchant_id' => $sellerID, 'site_id' => $siteId])->exists()) {
            return $this->returnJsonData(false, '该店铺已经授权给其他用户!');
        }

        $amazon = new AmazonBase();
        $amazon ->setAttibutes('AccessKeyID', $accessKeyID);
        $amazon ->setAttibutes('SellerID', $sellerID);
        $amazon ->setAttibutes('SecretKey', $secretKey);

        // 接口需要的参数列表，参与签名
        $sub_pramas = [
            'ReportType' => '_GET_MERCHANT_LISTINGS_DATA_',
        ];

        //接口的服务和版本号，不同的接口是不一样的，参与签名
        $service_and_version = '';
        try {
            $result = $amazon->request('GetReportRequestCount', [], $service_and_version);
        } catch (Exception $e) {
            return $this->returnJsonData(false, $e->getMessage());
        }

        if (!empty($result['Error'])) {
            return $this->returnJsonData(false, '账户信息错误');
        }

        $storeModel = new Store();
        $data = [
            'store_name' => $storeName,
            'user_id' => $this->_userId,
            'platform_id' => $platformId,
            'site_id' => $siteId,
            'merchant_id' => $sellerID,
            'accesskey_id' => $accessKeyID,
            'secret_key' => $secretKey,
        ];

        if (!($storeId = $storeModel->saveStore($data))) {
            return $this->returnJsonData(false, '创建店铺失败');
        }

        return $this->returnJsonData(true, '', ['shop_id' => $storeId]);

    }

    #重新授权
    public function actionRetry(){
        return $this->actionSend();
    }

}