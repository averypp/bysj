<?php
namespace app\controllers;
use Yii;
use yii\web\Controller;
use app\models\Product;
use app\models\SeaShellResult;
use yii\data\Pagination;
use app\models\Store;
use app\models\BadReview;
use app\models\BadReviewMonitor;
use app\models\Bidding;
use app\models\BiddingRules;
use app\models\BiddingLog;
/*
* add by SHB 2016-11-10
*/

Class BiddingController extends BaseController{
    public $layout = false;

    public $maxmonitor = 10;

    public function __construct($id, $module)
    {
        parent::__construct($id, $module);

        $this->initProductManagement();
    }

    public function actionIndex(){
        // var_dump($this->_request->get());die;
        $shopId = $this->_request->get('shopId');
        $ruleId = $this->_request->get('rid');
        $params = array(
            'title' => $this->_request->get('t'),
            'sku' => $this->_request->get('k'),
            'asin' => $this->_request->get('p'),
            'page_no' => $this->_request->get('page_no', 10),
            'page' => $this->_request->get('page', 1),
            'filter' => $this->_request->get('filter', 0),
        );


        if(empty($shopId)){
            $missing = "shopId Miss";
            return SeaShellResult::error($missing);
        }

        $view = "list";

        // $biddingModel = new Bidding();
        // $biddingGoods = $biddingModel->getBiddingGoods($params, $this->_shopId);

        $query = Bidding::find()
            ->leftJoin('sea_goods_sync_sku', 'sea_bidding.sku_id = sea_goods_sync_sku.id')
            ->leftJoin('sea_goods_sync_online', 'sea_bidding.goods_id = sea_goods_sync_online.id')
            ->where(['sea_bidding.shop_id'=>$this->_shopId]);
        switch ($params['filter']) {
            case '1':
                $query->andWhere(['sea_bidding.status' => 1]);
                break;
            case '2':
                $query->andWhere(['>', 'sea_bidding.competitors_count', 0]);
                break;
            case '3':
                $query->andWhere(['=', 'sea_bidding.competitors_count', 0]);
                break;
            case '4':
                $query->andWhere('sea_bidding.my_price + sea_bidding.my_price_fare = sea_bidding.mix_price');
                break;
            case '5':
                $query->andWhere("sea_bidding.my_price + sea_bidding.my_price_fare = sea_bidding.max_price");
                break;
            case '6':
                $query->andWhere(['=', 'sea_bidding.rules_id', 0]);
                break;
            case '7':
                $query->andWhere('sea_bidding.lower_price + sea_bidding.lower_price_far < sea_bidding.mix_price');
                break;
            default:
                break;
        }

        if($params['title']){
            $query->andWhere(['like', 'sea_goods_sync_online.title', $params['title']]);
        }
        if($params['sku']){
            $query->andWhere(['=', 'sea_goods_sync_sku.sku', $params['sku']]);
        }
        if($params['asin']){
            $query->andWhere(['=', 'sea_goods_sync_sku.asin', $params['asin']]);
        }
        if($ruleId){
            $query->andWhere(['=', 'sea_bidding.rules_id', $ruleId]);
        }

        $num = $query->count();
        $pages = new Pagination([
            'defaultPageSize' => $params['page_no'],
            'totalCount' => $num,
            'pageSize' => $params['page_no'],
        ]);

        $query->select(['sea_bidding.*', 'sea_goods_sync_sku.sku', 'sea_goods_sync_online.asin', 'sea_goods_sync_online.title']);
        $biddingGoods = $query->orderBy('id desc')->offset($pages->offset)
                    ->limit($pages->limit)/*->orderBy('sea_bad_review_monitor.id desc')*/->asArray()->all();

        $rules = BiddingRules::find(['shop_id'=>$this->_shopId])->asArray()->all();
        // ====================
        $data['goods'] = $biddingGoods;
        $data['rules'] = $rules;
        $data['pages'] = $pages;
        $data['totalCount'] = $num;

        $data['search_asin'] = empty($search_asin) ? "" : $search_asin;
        $data['requestUri'] = $this->getRequestUri('page_no');
        $data['shopInfo'] = $this->_shopInfo;

        $data['BRcount'] = $this->_BRcount;
        $data['filter'] = $params['filter'];
        $data['searchUri'] = $this->getRequestUri('filter');
        $data['href'] = 'list';
        return  $this->render($view, $data);
    }

    //批量/单个  启动/暂停智能调价 
    public function actionBatchEdit(){
        $ids = $this->_request->post('ids');
        $ids = explode(',' , $ids);
        if (!$this->_filterIds($ids)) {
            return $this->returnJsonData(['status' => 0], '操作失败');
        }
        $status = $this->_request->post('status');
        if( in_array($status, [0,1]) && $ids && is_array($ids) ){
            $allInfo = Bidding::find()->with('rules')->with('sku')->with('goods')->where(['id' => $ids])->asArray()->all();
            foreach ($allInfo as $singleInfo) {
                if($status == 1 && $singleInfo['rules_id'] <= 0){
                    return $this->returnJsonData(['status' => 0], '未设置商品调价规则');
                }
                Bidding::batchEditeBidding($singleInfo['id'], $status);
            }
            return $this->returnJsonData(['status' => 1], '操作成功');
        }
        return $this->returnJsonData(['status' => 0], '操作失败');
    }

    //批量清空调价规则 
    public function actionBatchCleanRule(){
        $ids = $this->_request->post('ids');
        $ids = explode(',' , $ids);
        if (!$this->_filterIds($ids)) {
            return $this->returnJsonData(['status' => 0], '操作失败!');
        }
        if(Bidding::batchCleanRule($ids)){
            Bidding::batchEditeBidding($ids, 0);
            return $this->returnJsonData(['status' => 1], '操作成功');
        }
        return $this->returnJsonData(['status' => 0], '操作失败!');
    }

    //批量删除调价商品 
    public function actionBatchRemoveGoods(){
        $ids = $this->_request->post('ids');
        $ids = explode(',' , $ids);
        if (! ($skuIds = $this->_filterIds($ids)) ) {
            return $this->returnJsonData(['status' => 0], '操作失败!');
        }
        if(Bidding::batchRemoveGoods($ids, $skuIds)){
            return $this->returnJsonData(['status' => 1], '操作成功');
        }
        return $this->returnJsonData(['status' => 0], '操作失败!');
    }

    private function _filterIds(&$ids)
    {
        if (!is_array($ids)) {
            settype($ids, 'array');
        }
        $ids = Bidding::find()->select(['id', 'goods_id', 'sku_id'])->where(['id' => $ids, 'shop_id' => $this->_shopId])->asArray()->all();

        if (!$ids) {
            return false;
        }

        $skuIds = array_column($ids, 'sku_id');
        $ids = array_column($ids, 'id');
        return $skuIds;
    }

    //设置商品调价
    public function actionEditBidding(){
        $id = $this->_request->post('id');
        $cost = $this->_request->post('ori_price');
        $mix_price = $this->_request->post('min_price');
        $max_price = $this->_request->post('max_price');
        $rules_id = $this->_request->post('rule_id');
        $my_price = $this->_request->post('my_price');
        if( ($my_price > $max_price) || ($my_price < $mix_price) || ($mix_price > $max_price) ){
            return $this->returnJsonData(['status' => 0], '价格数据错误!');
        }
        if( $id && is_numeric($cost) && is_numeric($mix_price) && is_numeric($max_price) ){
            $data['cost'] = $cost;
            $data['mix_price'] = $mix_price;
            $data['max_price'] = $max_price;
            $data['rules_id'] = $rules_id ? $rules_id : 0;
            if(Bidding::editBidding($id ,$data)){
                return $this->returnJsonData(['status' => 1], '操作成功');
            }
            return $this->returnJsonData(['status' => 0], '操作失败!');
        }
        return $this->returnJsonData(['status' => 0], '数据错误!');
    }

    public function actionRulelist(){
        $shopId = $this->_request->get('shopId');
        $id = $this->_request->get('id');
        $params = array(
            'page_no' => $this->_request->get('page_no', 10),
            'page' => $this->_request->get('page', 1)
        );
        if(empty($shopId)){
            $missing = "shopId Miss";
            return SeaShellResult::error($missing);
        }

        $view = "rulelist";
        $query = BiddingRules::find(['shop_id'=>$this->_shopId]);
        if ($id > 0) {
            $query->andFilterWhere(['id' => $id]);
        }
        //计算应用商品数<--------
        $biddingInfo = Bidding::getBidInfo($this->_shopId);
        if($biddingInfo && is_array($biddingInfo)){
            $countArray = [];
            foreach ($biddingInfo as $key => $value) {
                if( !isset($countArray[$value['rules_id']]) ){
                    $countArray[$value['rules_id']] = 1;
                } else {
                    $countArray[$value['rules_id']]++;
                }
            }
        }
        //------>
        $num = $query->count();
        $pages = new Pagination([
            'defaultPageSize' => $params['page_no'],
            'totalCount' => $num,
            'pageSize' => $params['page_no'],
        ]);
        $rules = $query->orderBy('id desc')->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        foreach ($rules as $key => $value) {
            if(isset($countArray[$value['id']])){
                $rules[$key]['seller_count'] = $countArray[$value['id']];
            }else{
                $rules[$key]['seller_count'] = 0;
            }
        }
        // ====================
        $data['rules'] = $rules;
        $data['pages'] = $pages;
        $data['totalCount'] = $num;

        $data['search_asin'] = empty($search_asin) ? "" : $search_asin;
        $data['requestUri'] = $this->getRequestUri('page_no');
        $data['shopInfo'] = $this->_shopInfo;

        $data['BRcount'] = $this->_BRcount;
        $data['href'] = 'rulelist';
        return  $this->render($view, $data);
    }

    public function actionEditRule(){
        if(!$_POST){
            $rid = intval($this->_request->get('rid'));
            $shopId = $this->_request->get('shopId');
            if($rid){
               $ruleInfo = BiddingRules::find()->where(['id' => $rid])->asArray()->one();
               $data['ruleInfo'] = $ruleInfo;
            }
            $view = "edit_rule";
            $data['BRcount'] = $this->_BRcount;
            $data['shopInfo'] = $this->_shopInfo;
            $data['shopId'] = $shopId;
            return  $this->render($view, $data);
        } else {
            $ruleInfo = $this->_request->post();

            if(!isset($ruleInfo['rule-name'])){
                return $this->returnJsonData(['status' => 0], '请填写规则名称!');
            }
            if(!isset($ruleInfo['competitors'])){
                return $this->returnJsonData(['status' => 0], '选择竞争对手!');
            }
            //echo "<pre>";
            //var_dump($ruleInfo);die;
            if(!$ruleInfo['rule-id']){
                if( BiddingRules::recordInfo($ruleInfo) ){
                   return $this->returnJsonData(['status' => 1], '操作成功');
                }
            } else {
                if( BiddingRules::editRulesInfo($ruleInfo, $this->_shopId)){
                   return $this->returnJsonData(['status' => 1], '操作成功');
                }
            }
            return $this->returnJsonData(['status' => 0], '操作失败!');
        }
    }

    public function actionRenderRuleInfo(){
        $rid = $this->_request->get('rid');
        $ruleInfo = BiddingRules::find()->with('types.items')->where(['id' => $rid])->asArray()->one();
        if(!$ruleInfo){
            return $this->returnJsonData(['status' => 1], '');
        }
        return $this->returnJsonData(['status' => 1], $ruleInfo);
    }

    //删除调价规则
    public function actionRemoveRule(){
        $ruleId = (int)$this->_request->post('rule_id');
        $shopId = (int)$this->_request->get('shopId');
        $count = Bidding::find(['rules_id' => $ruleId])->count();
        if($count){
            return $this->returnJsonData(['status' => 0], '有商品使用此规则，无法删除!');
        }
        if($ruleId && $shopId && BiddingRules::removeRule($ruleId, $shopId)){
            return $this->returnJsonData(['status' => 1], '操作成功');
        }
        return $this->returnJsonData(['status' => 0], '操作失败!');
    }

    public function actionLog(){
        $shopId = $this->_request->get('shopId');
        $search_asin = $this->_request->get('asin');
        $params = array(
            'page_no' => $this->_request->get('page_no', 10),
            'page' => $this->_request->get('page', 1)
        );

        if(empty($shopId)){
            $missing = "shopId Miss";
            return SeaShellResult::error($missing);
        }

        $view = "log";

        $query = BiddingLog::find();
        // $query = BiddingLog::find()
        //     //->leftJoin('sea_goods_sync_sku', 'sea_bidding.sku_id = sea_goods_sync_sku.id')
        //     ->leftJoin('sea_goods_sync_online', 'sea_bidding.goods_id = sea_goods_sync_online.id')
        //     ->where(['sea_bidding.shop_id'=>$this->_shopId]);
        if($search_asin){
            $query->Where(['like', 'sea_bidding_log.asin', $search_asin]);
        }
        // $query->select(['sea_bidding.*', 'sea_goods_sync_sku.sku', 'sea_goods_sync_sku.asin', 'sea_goods_sync_online.title']);


        $num = $query->count();
        $pages = new Pagination([
            'defaultPageSize' => $params['page_no'],
            'totalCount' => $num,
            'pageSize' => $params['page_no'],
        ]);
        
        $logs = $query->orderBy('id desc')->offset($pages->offset)->limit($pages->limit)->asArray()->all();

        // ====================

        $data['logs'] = $logs;
        $data['pages'] = $pages;
        $data['totalCount'] = $num;

        $data['search_asin'] = empty($search_asin) ? "" : $search_asin;
        $data['requestUri'] = $this->getRequestUri('page_no');
        $data['shopInfo'] = $this->_shopInfo;

        $data['BRcount'] = $this->_BRcount;
        $data['href'] = 'log';
        return  $this->render($view, $data);
    }


}


?>