<?php

namespace app\controllers;

use Yii;
use Exception;
use yii\web\Controller;
use app\models\GoodsSyncSku;
use app\models\GoodsSyncOnline;
use app\models\QueueUpdategoods;
use app\models\QueueSync;
use app\models\Bidding;
use yii\helpers\Json;
use yii\widgets\LinkPager;
use app\libraries\Queue;

class ProductOnlineController extends BaseController
{

    public $layout = false;
    public $enableCsrfValidation = false;
    private $_cachePrefix;

    public function __construct($id, $module)
    {
        parent::__construct($id, $module);
        $this->initProductManagement();
        $this->_cachePrefix = __CLASS__ . '_' . $this->_shopId;
    }

    public function actionIndex()
    {
        $params = array(
            'title' => $this->_request->get('t'),
            'sku' => $this->_request->get('k'),
            'asin' => $this->_request->get('p'),
            'page_no' => $this->_request->get('page_no', 10),
            'page' => $this->_request->get('page', 1),
            'is_stock' => $this->_request->get('stoc', 0),
        );

        $pages = null;
        $products = [];
        $pageString = '';
        $totalCount = 0;
        $shopInfo = $this->_shopInfo;
        $BRcount = $this->_BRcount;
        $stockUri = $this->getRequestUri('stoc');
        $pageUri = $this->getRequestUri('page_no');
        
        $syncStatus = QueueSync::find()
            ->where(['shop_id' => $this->_shopId])
            ->one();
        if ( !$syncStatus || $syncStatus['status'] != 0) {
            $syncGoodsModel = new goodsSyncOnline();
            $syncGoods = $syncGoodsModel->getSyncGoods($params, $this->_shopId);
            $products = $syncGoods['products'];
            $pages = $syncGoods['pages'];
        }
        
        if ($pages) {
            $pageString = LinkPager::widget([
                'pagination' => $pages,
                'options' => [
                    'class' => 'pagination page-bar',
                ],
                'hideOnSinglePage' => false,
                'nextPageLabel' => '下一页',
                'prevPageLabel' => '上一页',
                'firstPageLabel' => '首页', 
                'lastPageLabel' => '尾页', 
            ]);
            $totalCount = $pages->totalCount;
        }

        $data = compact('syncStatus','shopInfo','params','stockUri','pageUri','products','pageString','totalCount','BRcount');
        // print('<pre>');var_dump($data);die('</pre>');

        return $this->render('index', $data);
    }

    public function actionSyncShop()
    {
        // $cacheKey = 'sync';
        // if ($this->_getCache($cacheKey)) {
        //     return $this->returnJsonData(['status' => 0], '距离上次同步操作小于15分钟，请稍后再试');
        // }
        // $this->_setCache($cacheKey, 1, 900);

        $process = QueueSync::find()
            ->where(['shop_id' => $this->_shopId])
            ->one();
        if($process){
            if ($process['status']==0 ) {
                return $this->returnJsonData(['status' => 0], '正在同步中，请等待...');
            }else{
                $process->status = 0;
                $process->gmt_modified = date("Y-m-d H:i:s");
                $process->update();
            }
        }else{
            $queueSync = new QueueSync();
            if (!$queueSync->saveData($this->_shopId)) {
                return $this->returnJsonData(['status' => 0], '同步失败');
            }
        }

        $args = [
            'stepOne',
            ['shop_id' => $this->_shopId]
        ];
        $job = \app\libraries\Queue::enqueue('SyncGoods', $args, 'syncGoods_one');

        return $this->returnJsonData(['status' => 1], '产品同步任务已提交，请等待'); 
    }

    private function _setCache($key, $value, $expire = 600)
    {
        return Yii::$app->cache->set($this->_cachePrefix . $key, $value, $expire);
    }

    private function _getCache($key, $delete = false)
    {
        $key = $this->_cachePrefix . $key;
        $cache = Yii::$app->cache;
        if (!$cache->exists($key)) {
            return false;
        }
        $value = $cache->get($key);
        if ($delete) {
            $cache->delete($key);
        }
        return $value;
    }

    public function actionMultiModify($type)
    {

        $ids = $this->_request->post('ids');
        $con = $this->_request->post('con');
        $value = $this->_request->post('value');
        $pattern = $this->_request->post('pattern');
        $sale_from = $this->_request->post('sale_from');
        $sale_to = $this->_request->post('sale_to');

        $patternFormat = array('add' => '+', 'subtract' => '-',
            'multiply' => '*', 'divide' => '/', 'replace' => '');

        $sliceSql = [];
        $feilds = ['sale' => 'sale_price', 'price' => 'price', 'stock' => 'stock'];

        if (!isset($feilds[$type]) || !isset($patternFormat[$pattern])) {
            return $this->returnJsonData(['status' => 0], '非法请求');
        }
        if (!is_numeric($value) || !($ids = Json::decode($ids))) {
            return $this->returnJsonData(['status' => 0], '请求参数不合法，请重试');
        }
        if ($type == 'sale') {
            if (!$sale_from || !$sale_to) {
                return $this->returnJsonData(['status' => 0], '缺省参数');
            }
            $sliceSql['sales_begin_date'] = $sale_from;
            $sliceSql['sales_end_date'] = $sale_to;
        }
        
        if ($type == 'stock') {
            $cacheKey = 'stock';
        } else {
            $cacheKey = 'price';
        }
        if ($this->_getCache($cacheKey)) {
            return $this->returnJsonData(['status' => 0], '距离上次相同操作小于10分钟，请稍后再试');
        }
        $this->_setCache($cacheKey, 1);

        if ($patternFormat[$pattern]) {
            $sliceSql[$feilds[$type]] = "{$feilds[$type]} $patternFormat[$pattern] $value";
        } else {
            $sliceSql[$feilds[$type]] = $value;
        }

        // 临时修改数据库数据
        $skuIds = GoodsSyncSku::modifyPriceStock($ids, $sliceSql);
        if (!$skuIds) {
            return $this->returnJsonData(['status' => 0], '未更新任何数据！');
        }

        // 写入队列
        $args = [
            'sku_ids' => implode(',', $skuIds),
            'type' => $type,
            'shop_id' => $this->_shopId
        ];
        Queue::enqueue('UpdateGoods', $args, 'updateGoods');

        return $this->returnJsonData(['status' => 1], '更新任务已经提交,请等待任务完成!');

    }

    public function actionSingleBasic()
    {
        $pid = $this->_request->post('pid');
        $category = $this->_request->post('category') ?: 'all';
        $base = $this->_request->post('base');
        if (!$pid || !($base = Json::decode($base))) {
            return $this->returnJsonData(['status' => 0], '请求参数不合法，请重试');
        }

        $type = 'basic';
        if ($this->_getCache($type)) {
            return $this->returnJsonData(['status' => 0], '距离上次相同操作小于10分钟，请稍后再试');
        }
        $this->_setCache($type, 1);

        $_now = time();
        $gmt = date('Y-m-d H:i:s', $_now);
        $params = [
            'keywords' => serialize($base['KeyWords']),
            'bullet_points' => serialize($base['BulletPoints']),
            'description' => base64_encode($base['Description']),
            'item_type' => $base['ItemType'],
            'gmt_modified' => $gmt,
            'update_at' => $_now,
            // 'title' => $base['Title'][0]['title'],
        ];

            
        $goodsSyncOnline = new GoodsSyncOnline();
        $res = $goodsSyncOnline->updateGoodsById($pid, $this->_shopId, $params);

        if (!$res) {
            return $this->returnJsonData(['status' => 0], '未更新任何数据！');
        }
        // 写入队列
        $args = [
            'goods_id' => $pid,
            'type' => $type,
            'shop_id' => $this->_shopId
        ];
        Queue::enqueue('UpdateGoods', $args, 'updateGoods');

        return $this->returnJsonData(['status' => 1], '更新任务已经提交,请等待任务完成!');

    }

    public function actionSingleFeed()
    {

        $params = [
            'goods_id' => $this->_request->post('pid'),
        ];
        $category = $this->_request->post('category') ?: 'all';
        if ($category == 'single') {
            $params['sku'] = $this->_request->post('sku');
        }

        $syncGoodsModel = new goodsSyncOnline();
        $syncGoods = $syncGoodsModel->getSyncGoods($params, $this->_shopId);
        if (!$syncGoods) {
            return $this->returnJsonData(['status' => 0], '产品不存在或已删除！');
        }

        $json = [];
        $data['status'] = 1;
        foreach ($syncGoods as $key => $val) {
            $json['category'] = '';
            $json['description'] = base64_decode($val['description']);
            $json['bullets'] = unserialize($val['bullet_points']);
            $json['tags'] = unserialize($val['keywords']);
            foreach ($val['skus'] as $sku) {
                $json['title'][] = ['SKU' => $sku['sku'], 'Title' => $val['title']];
            }
        }

        $data['json'] = $json;

        return $this->returnJsonData($data);
    }

    public function actionSinglePrice()
    {
        $pid = $this->_request->post('pid');
        $info = $this->_request->post('info');

        $cacheKey = 'price';
        if ($this->_getCache($cacheKey)) {
            return $this->returnJsonData(['status' => 0], '距离上次相同操作小于10分钟，请稍后再试');
        }
        $this->_setCache($cacheKey, 1);

        if (!$pid || !($info = Json::decode($info)) || !isset($info['Price']) || !isset($info['SKU'])) {
            return $this->returnJsonData(['status' => 0], '请求参数不合法，请重试');
        }

        if (!is_numeric($info['Price']) || $info['Price'] <= 0) {
            return $this->returnJsonData(['status' => 0], '价格必须是大于0的数字或小数');
        }

        $sliceSql['price'] = $info['Price'];
        if ($info['SalePrice'] > 0) {
            if (!$info['SaleDateFrom'] || !$info['SaleDateTo']) {
                return $this->returnJsonData(['status' => 0], '请填写促销时间');
            }
            $sliceSql['sale_price'] = $info['SalePrice'];
            $sliceSql['sales_begin_date'] = $info['SaleDateFrom'];
            $sliceSql['sales_end_date'] = $info['SaleDateTo'];
        }

        $skuIds = GoodsSyncSku::modifyPriceStock($pid, $sliceSql, ['sku' => $info['SKU']]);
        if (!$skuIds) {
            return $this->returnJsonData(['status' => 0], '未更新任何数据！');
        }

        $args = [
            'sku_ids' => implode(',', $skuIds),
            'type' => 'saleprice',
            'shop_id' => $this->_shopId
        ];
        Queue::enqueue('UpdateGoods', $args, 'updateGoods');

        return $this->returnJsonData(['status' => 1], '更新任务已经提交,请等待任务完成!');

    }

    public function actionSingleStock()
    {
        $pid = $this->_request->post('pid');
        $info = $this->_request->post('info');

        $cacheKey = 'stock';
        if ($this->_getCache($cacheKey)) {
            return $this->returnJsonData(['status' => 0], '距离上次相同操作小于10分钟，请稍后再试');
        }
        $this->_setCache($cacheKey, 1);

        if (!$pid || !($info = Json::decode($info))) {
            return $this->returnJsonData(['status' => 0], '请求参数不合法，请重试');
        }

        $sliceSql['stock'] = $info['Stock'];

        $skuIds = GoodsSyncSku::modifyPriceStock($pid, $sliceSql, ['sku' => $info['SKU']]);
        if (!$skuIds) {
            return $this->returnJsonData(['status' => 0], '未更新任何数据！');
        }

        $args = [
            'sku_ids' => implode(',', $skuIds),
            'type' => 'stock',
            'shop_id' => $this->_shopId
        ];
        Queue::enqueue('UpdateGoods', $args, 'updateGoods');

        return $this->returnJsonData(['status' => 1], '更新任务已经提交,请等待任务完成!');
    }


    public function actionSingleLowestPrice()
    {
        $pid = $this->_request->post('pid');
        $sku = $this->_request->post('sku');

        $data = array(
            'status' => 1,
            'message' => 'success!',
            'price' => array(
                'FBMPrice' => array(
                    'Price' => 102.00,
                    'Ship' => 4.99,
                ),
                'BPrice' => array(
                    'Price' => '',
                    'Ship' => '',
                ),
                'FBAPrice' => array(
                    'Price' => '',
                    'Ship' => '',
                ),
            )
        );

        return $this->returnJsonData($data);
    }

    public function actionAdd_bidding()
    {
        // 限制添加的数量
        $maxTotal = 15;
        $count = Bidding::find()->where(['shop_id' => $this->_shopId])->count();
        if ($count >= $maxTotal) {
            return $this->returnJsonData(['status' => 0], "调价商品不能超过{$maxTotal}个");
        }

        $skuId = $this->_request->post('sku_id');
        $sku = GoodsSyncSku::findOne($skuId);
        if(Bidding::findOne(['sku_id' => $sku->id, 'shop_id' => $this->_shopId])){
            return $this->returnJsonData(['status' => 0], ' 商品不能重复添加!');
        }
        if (!$sku || $sku->shop_id != $this->_shopId) {
            return $this->returnJsonData(['status' => 0], ' 添加失败!');
        }
        if (!Bidding::addBidding($sku)) {
            return $this->returnJsonData(['status' => 0], ' 添加失败!');
        }
        $sku->is_adjustment_price = 1;
        if($sku->save()){
            return $this->returnJsonData(['status' => 1], ' 添加完成!');
        }
        return $this->returnJsonData(['status' => 0], ' 添加失败!');
    }
}