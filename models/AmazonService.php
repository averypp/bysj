<?php
namespace app\models;

use Yii;
use app\assets\AmazonBase;
use yii\helpers\ArrayHelper;
use Exception;
use app\libraries\MyHelper;
use app\libraries\Queue;

/**
* 定时任务 处理amazon数据 service
*/
class AmazonService 
{
    private static $_status = ['draft' => 0, 'waiting' => 1, 'dealing' => 2, 'failed' => 3, 'success' => 4];
	private static $_success = ['success' => "success", 'error' => "error"];

	/**
	* [商品发布到亚马逊] 把本地商品提交到亚马逊平台上
	* 1\保存提交信息（提交时间，提交接口返回的 feedSubmissionId）
	* 2\更新 任务表状态（由待提交到已提交（待查询结果））
	*/
    function actionPubtoamazon($good_id, $store_id = 0)
    {

        $feeds_info = AmazonFeeds::findOne(['good_id' => $good_id]);
        if (!$feeds_info || !$feeds_info['data']) {
            throw new Exception("Feeds data not exists");
        }

        $data = $this->setAmazonKey($store_id);
        $newapi = new \app\assets\Amazon($data['merchant_id'], $data['accesskey_id'], $data['secret_key'], $data['serviceUrl'], $data['marketplace_id']);
        $result = $newapi->pubToAmazon($feeds_info['data']);

        // 提交失败重新放入队列
        if (!$result) {
            throw new Exception("Submit data failed");
        }

        $feeds_info->FeedSubmissionId = $result['FeedSubmissionId'];
        $feeds_info->FeedType = $result['FeedType'];
        $feeds_info->gmt_modified = date('Y-m-d H:i:s');
        $feeds_info->SubmittedDate = date("Y-m-d H:i:s", strtotime($result['SubmittedDate']));
        $feeds_info->FeedProcessingStatus = $result['FeedProcessingStatus'];
        $feeds_info->save();

        return $result['FeedSubmissionId'];
    }

    /**
    * [获取发布结果]获取发布商品到亚马逊平台的结果
    * 
    * 1\amazonFeeds 表存储发布结果（失败or成功）
    * 2\更新产品的发布状态（失败 or 成功）
    * 3\更新定时任务已处理完（不管结果是 成功还是失败）
    * 4\
    */
    function getSubmissionResult($good_id, $store_id, $submission_id)
    {
        $storeInfo = Store::getInfoById($store_id);
        if (!$storeInfo) {
            throw new Exception("Store not exists or delete");
        }
        
        $siteInfo = Platform::findById($storeInfo['site_id']);
        if (!$siteInfo) {
            throw new Exception("Site not exists or delete");
        }

        $amazon = new AmazonBase();
        $amazon ->setAttibutes('SellerID', $storeInfo['merchant_id']);
        $amazon ->setAttibutes('AccessKeyID', $storeInfo['accesskey_id']);
        $amazon ->setAttibutes('SecretKey', $storeInfo['secret_key']);
        $amazon ->setAttibutes('EndPoint', $siteInfo['api_host']);

        //接口的服务和版本号，不同的接口是不一样的，参与签名
        $service_and_version = '';
        try {
            $reportInfo = $amazon->requestErrorReturn('GetFeedSubmissionResult', ['FeedSubmissionId'=>$submission_id], $service_and_version);
        } catch (Exception $e) {
            throw new Exception((string)$e);
        }

        if (!$reportInfo) {
            throw new Exception("Request Error");
        }

        return $amazon->getReturnErrorMsg($reportInfo);

        // preg_match_all('/\d{1,}/', $reportInfo, $matches);
        // $matches[0][0] == $matches[0][1] && $matches[0][1]>=1
        
        // old deal
        /*$customer = AmazonFeeds::findOne(['good_id' => $good_id]);
        $customer->status = 'processed';
        if (!$errorInfo) {
            $pubStatus = 4;
            $customer->success = 'success';
        } else {
            $customer->results = implode(" ", $errorInfo);
            $customer->success = 'error';
            $pubStatus = 3;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$customer->save()) {
                throw new Exception("AmazonFeeds save failed");
            }
            //更新发布状态
            if (!Product::updatePubStatus($good_id, $pubStatus)) {
                throw new Exception("Update pub_status failed");
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw new Exception($e->getMessage());
        }

        return true;*/
        //更新任务param
        //请求 获取线上数据
        /*if ($customer->success == 'success') {
            //api
            $data = $this->setParamForGetUpSuccessReturnValue($store_id, $good_id);
            $dataArray = $this->getUpSuccessReturnValue($data);
            $goodsOnline['goodsId'] = $good_id;
            $goodsOnline['shopId'] = $store_id;
            $goodsOnline['asin'] = $dataArray['ASIN'];
            $goodsOnline['image_url'] = $dataArray['SmallImage'];
            $goodsOnline['title'] = $dataArray['Title'];
            $bulletPoints = array();
            foreach ($dataArray['BulletPoint'] as $key => $value) {
                array_push($bulletPoints, $value);
            }
            if(!empty($bulletPoints)){
                $goodsOnline['bullet_points'] = serialize($bulletPoints);

            }
        }*/

    }

    /**
    * [查询线上产品部分信息]
    * @param  marketplaceId 来自站点表
    * @param  idType 查询参数类型 支持 ASIN/UPC 等参数值查询
    * @param  idList 查询参数类型的值
    * @return   Array(
                    [ASIN] => B01HROLXV6
                    [MarketplaceId] => ATVPDKIKX0DER
                    [BulletPoint] => Array
                        (
                            [0] => f444
                            [1] => h44
                            [2] => hgh444
                            [3] => hj444
                            [4] => hhg444
                        )
                    [ListPrice] => 342.00
                    [SmallImage] => http://ecx.images-amazon.com/images/I/41ILf8wCXcL._SL75_.jpg
                    [Title] => goodtixue
                ) 
    */
    function getUpSuccessReturnValue($data){
        //print_r($data);
        $newapi = new \app\assets\Amazon($data['merchant_id'], $data['accesskey_id'], $data['secret_key'], $data['serviceUrl'], $data['marketplaceId']);
        $result = $newapi->getUpSuccessReturnValue($data['marketplaceId'], $data['idType'], $data['idList']);
        return $result;
    }

    function setParamForGetUpSuccessReturnValue($store_id,$goods_id){
        $storeInfo = Store::getInfoById($store_id);
        $platform = Platform::findById($storeInfo['site_id']);
        $goodInfo = Product::findById($goods_id);
        $goodSoldInfos = GoodsSoldInfo::findByGoodsId($goods_id);
        $idList = array();
        foreach ($goodSoldInfos as $key => $value) {
            //组装查询值 例如 isType：UPC 则组装UPC参数值
            array_push($idList, $value['external_product_id']);
        }
        $data['merchant_id'] = $storeInfo['merchant_id'];
        $data['accesskey_id'] = $storeInfo['accesskey_id'];
        $data['secret_key'] = $storeInfo['secret_key'];

        $data['marketplaceId'] = $platform['marketplace_id'];

        $data['idType'] = $goodInfo['external_product_id_type'];
        $data['idList'] = $idList;
        return $data;
    }

    Public function setAmazonKey($shopId){
        $storeInfo = Store::getInfoById($shopId);
        $data['merchant_id'] = $storeInfo['merchant_id'];
        $data['accesskey_id'] = $storeInfo['accesskey_id'];
        $data['secret_key'] = $storeInfo['secret_key'];

        $siteInfo = Platform::findById($storeInfo['site_id']);
        $data['serviceUrl'] = "https://".$siteInfo['api_host'];
        $data['marketplace_id'] = $siteInfo['marketplace_id'];
        return $data;
    }

    //同步数据 分三步走：1、发送要同步的请求 2、获取reportId 3、携带reportId获取结果 
    

    //1、发送要同步的请求
    function syncOne($shopId, $id=0){

        $data = $this->setAmazonKey($shopId);//参数shopId
        $newapi = new \app\assets\Amazon($data['merchant_id'], $data['accesskey_id'], $data['secret_key'], $data['serviceUrl'], $data['marketplace_id']);

        $result = $newapi->requestReport($reportType = '_GET_MERCHANT_LISTINGS_DATA_');

        return $result;

        // if($result){
        //     echo 'Sync Online RequestReport success. ShopID: '.$shopId."\n";
        //     $process = QueueSync::syncComplete($id, 1);
        // }else{
        //     echo 'Sync Online RequestReport failed. ShopID: '.$shopId."\n";
        // }
    }

    //2、获取reportId
    function syncTwo($shopId, $id=0){
        $data = $this->setAmazonKey($shopId);//参数shopId
        $newapi = new \app\assets\Amazon($data['merchant_id'], $data['accesskey_id'], $data['secret_key'], $data['serviceUrl'], $data['marketplace_id']);

        $time = 0;
        do{
            $time++;
            if( $time >= 5 ){   //5次未取到, return false
                // echo "getReportID failed 5 times"."\n";
                throw new Exception("getReportID failed 5 times 同步数据异常");
                return false;
            }else{
                sleep(10);
                $generatedReportId = $newapi->getReportRequestList();
            }
        }while(empty($generatedReportId));
        
        return $generatedReportId;

        // if($generatedReportId){
        //     $process = QueueSync::syncComplete($id, 2, $generatedReportId);
        //     echo 'getReportID success generatedReportId: '.$generatedReportId."\n";
        // }else{
        //     echo "同步数据异常"."\n";
        // }
    }

    //3、report获取结果
    function syncThree($shopId, $generatedReportId, $id=0){
        $data = $this->setAmazonKey($shopId);//参数shopId
        $newapi = new \app\assets\Amazon($data['merchant_id'], $data['accesskey_id'], $data['secret_key'], $data['serviceUrl'], $data['marketplace_id']);
        $result = $newapi->getReport($generatedReportId);
        //var_dump($result);
        return $result;

        // if($result){
        //     foreach ($result as $key => $value) {
        //         $saveData['task_id'] = $id; 
        //         $saveData['shop_id'] = $shopId; 
        //         $saveData['content'] = base64_encode(serialize(array_map('utf8_encode', $value)));
        //         $saveData['status'] = 0;
        //         $saveData['gmt_create'] = date("Y-m-d H:i:s");
        //         $saveData['gmt_modified'] = $saveData['gmt_create'];
        //         $saveResult = QueueSyncMiddle::saveData($saveData);
        //     }
        //     if($saveResult){
        //         echo "同步线上数据存到中间表 ok\n";
        //         $process =  QueueSync::syncComplete($id, 3, $generatedReportId);
        //     }
        // }else{
        //     echo "同步线上数据存到中间表之获取线上数据异常"."\n";
        // }
    }

    function syncFour($shopId, $content,/* $task_id,*/ $id=0){
        $result = unserialize(base64_decode($content));
        //print_r($result);
        $isUpdateOrInsertData = $this->isUpdateOrInsertData($result, $shopId);
        $saveResult = false;
        if(!empty($isUpdateOrInsertData['insertData'])){
            echo '新增SKU: '.$isUpdateOrInsertData['insertData']['seller-sku'].' -- '.date('Y-m-d H:i:s')."\n";
            // try {
            $saveResult = $this->saveGoodsSyncOnlineData($isUpdateOrInsertData['insertData'], $shopId);
            // } catch (\Exception $e) {
                // throw new \Exception($e->getMessage(), 1);
            // }
            // $saveResult = true;
        }elseif (!empty($isUpdateOrInsertData['validUpdateData'])) {
            echo '更新SKU: '.$isUpdateOrInsertData['validUpdateData']['seller-sku'].' -- '.date('Y-m-d H:i:s')."\n";
            $saveResult = $this->updateGoodsSyncOnlineData($isUpdateOrInsertData['validUpdateData'], $shopId);
            //var_dump($isUpdateOrInsertData['validUpdateData']);die;
            // $is_delete = $this->deleteGoodsSyncOnlineData($isUpdateOrInsertData['validUpdateData'], $shopId);
            // if($is_delete){
            //     $saveResult = $this->saveGoodsSyncOnlineData($isUpdateOrInsertData['validUpdateData'], $shopId);
            // }
            // $saveResult = true;
        }else{
            $saveResult = true;
            echo "此次暂无数据更新\n";
        }

        return $saveResult;
        //更新任务表
        // if($saveResult){
        //     if(QueueSyncMiddle::syncComplete($id)){
        //         echo "中间表任务完成,记录删除\n";
        //         $process =  QueueSync::syncComplete($task_id, 4);
        //     }
        // }
    }

//按asin查询  已存在 则更新，不存在则新建  //sku
    function isUpdateOrInsertData($result, $shopId){
        $param['shopId'] = $shopId;
        //亚马逊返回语言设置为非英文
        if(isset($result['seller-sku'])){
            $param['skus'] = $result['seller-sku'];//$sku;
        }else{
            return array();
        }
        $exitGoodsOnlines = GoodsSyncSku::selectByParam($param);
        $updateData = array();
        $validUpdateData = array();
        $insertData = array();
        if(empty($exitGoodsOnlines)){
            $insertData = $result;
        }else{
            $validUpdateData = $result;
        }
        return compact("validUpdateData","insertData");
    }

    //按asin查询  已存在 则更新，不存在则新建
    function isUpdateOrInsertDataBack($result){
        $asin = array();
        foreach ($result as $key => $value) {
            array_push($asin, $value['asin1']);
        }
        $param['asin'] = $asin;
        $exitGoodsOnlines = GoodsSyncSku::selectByParam($param);
        $updateData = array();
        $validUpdateData = array();
        $insertData = array();
        if(empty($exitGoodsOnlines)){
            echo "不存在 全部新增";
            //不存在 则全部新增
            $insertData = $result;
        }else{
            //存在 则区分是全部更新 还是部分更新 部分新增
            $validAsins = ArrayHelper::getColumn($exitGoodsOnlines,"asin");//取出本地数据中的所有asin值
            $validSyncOnlineIds = ArrayHelper::getColumn($exitGoodsOnlines,"id");//取出本地数据中的id 查询本地sku所用
            $asinKeyArray = ArrayHelper::index($exitGoodsOnlines,"asin");
            foreach ($result as $key => $value) {
                if(in_array($value['asin1'], $validAsins)){
                    //说明 此在线商品 已存在本地表中,是否做更新操作 进一步判断库存 是否变更
                    array_push($updateData, $value);
                }else{
                    array_push($insertData, $value);
                }
            }
            if(!empty($updateData)){
                $validSkus = ArrayHelper::getColumn($updateData,"seller-sku");
                $goodsSyncSkuQueryParam['goodsOnlineIds'] = $validSyncOnlineIds; 
                $goodsSyncSkuQueryParam['skus'] = $validSkus; 
                $goodsSyncSkuResult = GoodsSyncSku::selectByParam($goodsSyncSkuQueryParam);
                foreach ($updateData as $key => $value) {
                    $asin = $value['asin1'];
                    $exitGoodsOnline = $asinKeyArray[$asin];
                    $goodsOnlineId = $exitGoodsOnline['id'];
                    foreach ($goodsSyncSkuResult as $skuKey => $skuValue) {
                        # 是否做更新操作 进一步判断库存 是否变更
                        if($goodsOnlineId==$skuValue['goods_online_id'] && $value['seller-sku'] == $skuValue['sku'] && $value['quantity'] != $skuValue['stock']){
                            $updateStockData['id'] = $skuValue['id'];
                            $updateStockData['oldStock'] = $skuValue['stock'];
                            $updateStockData['stock'] = $value['quantity'];
                            array_push($validUpdateData, $updateStockData);
                        }
                    }
                }
            }

        }

        return compact("validUpdateData","insertData");
    }

    //
    function deleteGoodsSyncOnlineData($updateData, $shopId){
            $is_delete = GoodsSyncSku::deleteGoodsSyncOnlineData($updateData, $shopId);
            return $is_delete;
    }

    //同步在线数据：本地已经同步过 有变更 则更新
    function updateGoodsSyncOnlineData($updateData, $shopId){
        if(empty($updateData)){
            return false;
        }
        $sku = $updateData['seller-sku'];
        $data = $this->setRelationParamForGetUpSuccessReturnValue($shopId, $sku);
        $psData = $this->getPriceAndShippingForSKU($data);
        if(empty($psData)){
            echo "Amazon getPriceAndShippingForSKU no data result";
            return null;
        }

        $updateData['price'] = isset($psData['price'])?$psData['price']:$insertData['price'];
        $updateData['shipping_fee'] = isset($psData['shipping_fee'])?$psData['shipping_fee']:$insertData['shipping_fee'];
        $updateData['fulfillment_channel'] = isset($psData['fulfillment_channel'])?$psData['fulfillment_channel']:'';
        // var_dump($updateData);die;
        return GoodsSyncSku::updateGoodsSyncSkuStock($updateData, $shopId);

    }

    //同步在线数据：本地没有同步过 直接新增
    function saveGoodsSyncOnlineData($insertData,$shopId){
        if(empty($insertData)){
            return false;
        }
        //var_dump($insertData);die;
        //区分主从在线商品 start
        //try {
            $realInsertData = $this->getRelationsOnlineData($insertData, $shopId);
        // } catch (\Exception $e) {
        //     throw new \Exception($e->getMessage(), 1);
                        
        // }
        if(empty($realInsertData)){
            echo "Amazon getUpSuccessReturnValue no data result";
            return false;
        }
        //var_dump($realInsertData);die;
        //end
        //获取此次PASIN
        //$batchAsin = ArrayHelper::getColumn($realInsertDatas,"asin1");
        $batchAsin = $realInsertData['asin1'];

        $param['asins'] = $batchAsin;
        $param['shopId'] = $shopId;
        $exitGoodsOnlines = GoodsSyncOnline::selectByParam($param);
        $exitGoodsSkus = GoodsSyncSku::selectByParam($param);

        $data['title'] = $realInsertData['item-name'];
        $data['description'] = base64_encode($realInsertData['item-description']);
        $data['asin'] = $realInsertData['asin1'];
        $data['shopId'] = $shopId;
        $data['image_url'] = $realInsertData['image-url'];
        $data['lastSyncAt'] = time();

        //事务  多表更新操作
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $goodsOnlineInfoId = 0;
            if( !empty($exitGoodsOnlines) && Yii::$app->myHelp->deepInArray($realInsertData['asin1'], $exitGoodsOnlines)){            
                $exitGoodsSkusMap = ArrayHelper::map($exitGoodsSkus,"sku","goods_online_id");
                $exitGoodsOnlinesMap = ArrayHelper::map($exitGoodsOnlines,"asin","id");
                $goodsOnlineInfoId = $exitGoodsOnlinesMap[$realInsertData['asin1']];
            }else{
                $goodsOnlineInfo = GoodsSyncOnline::createGoodsSyncOnline($data);
                if(empty($goodsOnlineInfo->id)){
                    throw new Exception("同步SKU操作，本地主表保存异常", 1);
                }
                $goodsOnlineInfoId = $goodsOnlineInfo->id;
            }
                        
            if(isset($realInsertData['entry'])){
                foreach ($realInsertData['entry'] as $key => $value) {
                    if(isset($exitGoodsSkusMap[$value['seller-sku']])){
                        $goodsOnlineInfoId = $exitGoodsSkusMap[$value['seller-sku']];
                    }

                    $goodsOnlineSkuData['sku'] = $value['seller-sku'];
                    $goodsOnlineSkuData['stock'] = $value['quantity']?:0;
                    $goodsOnlineSkuData['asin'] = $value['asin1'];
                    $goodsOnlineSkuData['salePrice'] = $value['price'];
                    $goodsOnlineSkuData['price'] = $value['price'];
                    $goodsOnlineSkuData['current_price'] = $value['price'];
                    $goodsOnlineSkuData['goodsOnlineId'] = $goodsOnlineInfoId;
                    $goodsOnlineSkuData['shop_id'] = $shopId;
                    $goodsOnlineSkuData['shipping_fee'] = $value['shipping_fee']?:0;
                    $goodsOnlineSkuData['fulfillment_channel'] = $value['fulfillment_channel'];
                    if(!GoodsSyncSku::createGoodsSyncSku($goodsOnlineSkuData) ){
                        throw new Exception("Error Processing createGoodsSyncSku", 1);
                    }
                }
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            echo "本地保存接口 异常：".$e;
            return false;
        }
        return true;
    }

    //获取在线商品信息（带关联关系的）
    function getRelationsOnlineData($insertData,$shopId){
        if(empty($insertData)){
            return null;
        }
        $sku = $insertData['seller-sku'];
        $data = $this->setRelationParamForGetUpSuccessReturnValue($shopId, $sku);
        $psData = $this->getPriceAndShippingForSKU($data);
        $dataArray = $this->getUpSuccessReturnValue($data);
        if(empty($dataArray)){
            echo "Amazon getUpSuccessReturnValue no data result";
            return null;
        }
        if(empty($psData)){
            echo "Amazon getPriceAndShippingForSKU no data result";
            return null;
        }
        $insertData['price'] = isset($psData['price'])?$psData['price']:$insertData['price'];
        $insertData['shipping_fee'] = isset($psData['shipping_fee'])?$psData['shipping_fee']:$insertData['shipping_fee'];
        $insertData['fulfillment_channel'] = isset($psData['fulfillment_channel'])?$psData['fulfillment_channel']:'';
        //var_dump($dataArray);die;
        $realInsertData = array();
        
        $asin = $insertData['asin1'];
        if(isset($dataArray['ASIN'])){
            $pAsin = $dataArray['ParentASIN'];
            if(empty($pAsin)){
                //表示这个asin没有组合售卖商品
                $realInsertData['asin1'] = $asin;
            }else{
                $realInsertData['asin1'] = $pAsin;
            }
            $realInsertData['item-name'] = $insertData['item-name'];
            $realInsertData['image-url'] = $dataArray['SmallImage'];
            $realInsertData['item-description'] = $insertData['item-description'];
            $realInsertData['entry'][] = $insertData;
        }
            
        return $realInsertData;
    }

    function getPriceAndShippingForSKU($data){
        //print_r($data);
        $newapi = new \app\assets\Amazon($data['merchant_id'], $data['accesskey_id'], $data['secret_key'], $data['serviceUrl'], $data['marketplaceId']);
        $result = $newapi->getPriceAndShippingForSKU($data['marketplaceId'], $data['idList']);
        return $result;
    }

    function setRelationParamForGetUpSuccessReturnValue($store_id, $skus){
        $storeInfo = Store::getInfoById($store_id);
        $platform = Platform::findById($storeInfo['site_id']);
        
        $data['merchant_id'] = $storeInfo['merchant_id'];
        $data['accesskey_id'] = $storeInfo['accesskey_id'];
        $data['secret_key'] = $storeInfo['secret_key'];

        $data['marketplaceId'] = $platform['marketplace_id'];
        $data['serviceUrl'] = "https://".$platform['api_host'];

        $data['idType'] = "SellerSKU";
        $data['idList'] = $skus;
        return $data;
    }

    /**
    *
    * @param id 任务ID
    * @param shopId 店铺ID
    * @param type 更新类型（基本信息、价格、库存、促销信息）
    * @param skuIds skuID（来自sea_goods_sync_sku ID）
    * @param goodsId 商品ID（来自sea_goods_sync_online） 
    *
    *
    */
    public function updateGoodsMain($task)
    {
        $sku_ids = isset($task['sku_ids']) ? $task['sku_ids'] : [];
        switch ($task['type']) {
            case 'basic':
                $result = $this->changeBasicInfo($task['shop_id'], $task['goods_id']);
                break;
            case 'price':
                $result = $this->changePrice($task['shop_id'], $sku_ids);
                if($result){
                    GoodsSyncSku::syncCurrentPrice($sku_ids);
                }
                break;
            case 'sale':
            case 'saleprice':
                $result = $this->changeSalePrice($task['shop_id'], $sku_ids);
                if($result){
                    GoodsSyncSku::syncCurrentPrice($sku_ids);
                }
                break;
            case 'stock':
                $result = $this->changeInventory($task['shop_id'], $sku_ids);
                break;
            default:
                throw new \Exception("type not match.", 1);
                break;
        }

        return $result;
    }

    function syncCurrentPrice($ids){
        GoodsSyncSku::syncCurrentPrice($ids);
    }

    # 更新基本信息
    function changeBasicInfo($shopId,$goodsId){
        $data = $this->setAmazonKey($shopId);

        $infoData = GoodsSyncOnline::getSyncGoodsInfo($goodsId);
        $bullet_points = unserialize($infoData['bullet_points']);
        $search_terms = unserialize($infoData['keywords']);
        $data_array = [
            'Message' =>[
                    'MessageID' => 1,
                    'OperationType' => 'PartialUpdate',
                    'Product' =>[
                        'SKU' => $infoData['sku'],
                        'DescriptionData' => [
                            'Title' =>$infoData['title'],
                            'Description' => base64_decode($infoData['description']),
                            'BulletPoint1' => isset($bullet_points[0]) ? $bullet_points[0] : ' ',
                            'BulletPoint2' => isset($bullet_points[1]) ? $bullet_points[1] : ' ',
                            'BulletPoint3' => isset($bullet_points[2]) ? $bullet_points[2] : ' ',
                            'BulletPoint4' => isset($bullet_points[3]) ? $bullet_points[3] : ' ',
                            'BulletPoint5' => isset($bullet_points[4]) ? $bullet_points[4] : ' ',
                            'SearchTerms1' => isset($search_terms[0]) ? $search_terms[0] : ' ',
                            'SearchTerms2' => isset($search_terms[1]) ? $search_terms[1] : ' ',
                            'SearchTerms3' => isset($search_terms[2]) ? $search_terms[2] : ' ',
                            'SearchTerms4' => isset($search_terms[3]) ? $search_terms[3] : ' ',
                            'SearchTerms5' => isset($search_terms[4]) ? $search_terms[4] : ' '
                        ]
                    ]
            ]
        
        ];
        $xml = MyHelper::arrayBuildXml($data['merchant_id'], $data_array, $type = 'Product');
        $newapi = new \app\assets\Amazon($data['merchant_id'], $data['accesskey_id'], $data['secret_key'], $data['serviceUrl'], $data['marketplace_id']);
        $result = $newapi->changeProductInfo($xml);
        return $result;
    }

    # 更新促销信息
    function changeSalePrice($shopId,$skuIds){
        $data = $this->setAmazonKey($shopId);
        $param['ids'] = explode(",",$skuIds);
        $changeData = GoodsSyncSku::selectByParam($param);
        $data_array = [];
        foreach ($changeData as $key => $value) {
            $data_array[$key] = [
                'Message' =>[
                        'MessageID' => $key+1,
                        'Price' =>[
                            'SKU' => $value['sku'],
                            'StandardPrice' => $value['price'],
                        ],
                ],
            ];
            if ($value['sales_begin_date'] && $value['sales_end_date']) {
                $data_array[$key]['Message']['Price']['Sale'] = [
                                 'StartDate' => MyHelper::Time2Gtime($value['sales_begin_date']),
                                 'EndDate' => MyHelper::Time2Gtime($value['sales_end_date']),
                                 'SalePrice' => $value['sale_price']
                            ];
            }
        }
        $xml = MyHelper::arrayBuildXml($data['merchant_id'], $data_array, $type = 'Price');
        $newapi = new \app\assets\Amazon($data['merchant_id'], $data['accesskey_id'], $data['secret_key'], $data['serviceUrl'], $data['marketplace_id']);
        $result = $newapi->changePrice($xml);
        return $result;
    }

    //更新价格
    function changePrice($shopId,$skuIds){
        $data = $this->setAmazonKey($shopId);
        $param['ids'] = explode(",",$skuIds);
        $changeData = GoodsSyncSku::selectByParam($param);
        $data_array = [];
        foreach ($changeData as $key => $value) {
            $data_array[] = [
                'Message' =>[
                        'MessageID' => $key+1,
                        'Price' =>[
                            'SKU' => $value['sku'],
                            'StandardPrice' => $value['price'],
                        ],
                ],
            ];
        }
        $xml = MyHelper::arrayBuildXml($data['merchant_id'], $data_array, $type = 'Price');
        $newapi = new \app\assets\Amazon($data['merchant_id'], $data['accesskey_id'], $data['secret_key'], $data['serviceUrl'], $data['marketplace_id']);
        $result = $newapi->changePrice($xml);
        return $result;
    }


    function changeInventory($shopId,$skuIds){
        $data = $this->setAmazonKey($shopId);
        $param['ids'] = explode(",",$skuIds);
        $changeData = GoodsSyncSku::selectByParam($param);
        $data_array = [];
        foreach ($changeData as $key => $value) {
            $data_array[] = [
                'Message' =>[
                        'MessageID' => $key+1,
                        'OperationType' => 'Update',
                        'Inventory' =>[
                            'SKU' => $value['sku'],
                            'Quantity' => $value['stock'],
                        ],
                ],
            ];
        }
        $xml = MyHelper::arrayBuildXml($data['merchant_id'], $data_array, $type = 'Inventory');
        $newapi = new \app\assets\Amazon($data['merchant_id'], $data['accesskey_id'], $data['secret_key'], $data['serviceUrl'], $data['marketplace_id']);
        $result = $newapi->changeInventory($xml);
        return $result;
    }

    function getLowestPricedOffersForSKU($shopId, $sellerSKU, $itemCondition){
        $data = $this->setAmazonKey($shopId);
        $newApi = new \app\assets\Amazon($data['merchant_id'], $data['accesskey_id'], $data['secret_key'], $data['serviceUrl'], $data['marketplace_id']);
        $result = $newApi->getLowestPricedOffersForSKU($data['marketplace_id'], $sellerSKU, $itemCondition = 'New');
        return $result;

    }
}

 ?>