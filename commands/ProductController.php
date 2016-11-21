<?php

namespace app\commands;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use app\models\Product;
use app\models\Store;
use app\models\QueueUpdategoods;
use app\models\QueuePostgoods;
use app\models\QueueSyncMiddle;
use app\models\QueueSync;
use app\models\AmazonService;

class ProductController extends Controller
{
    private $_status = ['draft' => 0, 'waiting' => 1, 'dealing' => 2, 'failed' => 3, 'success' => 4];
    private $_dealing_status = ['upload' => 'upload', 'translate' => 'translate'];
    private $_boolean = ['true' => "N", 'false' => "Y"];
    private $_taskStatus = ['dealing' => 0, 'request' => 1, 'reportid' => 2, 'middle' => 3, 'result' => 4];
    private $_platformService = [];


    public function actionTest(){
        $amazonService = new AmazonService();
        $result = $amazonService->getLowestPricedOffersForSKU($shopId = 2, $sellerSKU = '32-OK6T-EMN0', $itemCondition = 'New');
        var_dump($result);
    }
        //00-MY52-IQLS

    /**
    * [定时任务 把发布中的产品 上传到亚马逊]
    *   0、定时任务每两分钟执行一次
    *   1、先取出所有的店铺info
    *   2、再取出这些店铺在处理中的产品，各一条
    *   3、再调亚马逊的接口
    */
    public function actionUpload()
    {
        $_now = time();
        $this->_stdOut('upload', 'start');
        $tasks = $this->_findModels(0);
        if (!$tasks) {
            die("no data... \n\n");
        }
        $amazonService = new AmazonService();
        foreach ($tasks as $key => $task) {
            $this->_stdOut("Public Amazon : goods_id -> {$task['goods_id']}");
            try {
                $feedSubmissionId = $amazonService->actionPubtoamazon($task['goods_id'], $task['shop_id']);
                $task->post_status = 1;
                $task->submission_id = $feedSubmissionId;
                $task->post_at = $_now;
                $task->save();
                $this->_stdOut('Public Amazon Success');
            } catch (\Exception $e) {
                $this->_stdOut($e->getMessage());
            }
            $this->_stdOut("Public Amazon end");
        }
        $this->_stdOut("upload", 'Done');
        exit(0);
    }

    public function actionGetResult()
    {
        $this->_stdOut('Get-Result', 'start');
        $tasks = $this->_findModels(1);
        if (!$tasks) {
            die(" no data... \n\n");
        }
        $amazonService = new AmazonService();
        foreach ($tasks as $key => $task) {
            $this->_stdOut("Get Public Result : {$task['goods_id']}");
            try {
                $amazonService->getSubmissionResult($task['goods_id'], $task['shop_id'], $task['submission_id']);
                //更新任务获取结果状态
                if (!$task->delete()) {
                    throw new Exception("Delete queuePostgoods failed", 1);
                }
            } catch (\Exception $e) {
                $this->_stdOut($e->getMessage());
            }
            $this->_stdOut("Get Public Result end");
        }
        $this->_stdOut('Get-Result', 'Done');
        die(0);
    }

    private function _stdOut($msg, $flag = null, $hasDate = false)
    {
        $date = date('Y-m-d H:i:s');
        if ($flag !== null) {
            $tr = "\n";
            if (strtolower($flag) == 'done') {
                $tr = "\n\n";
            } 
            fwrite(STDOUT, "*********** $date $msg $flag ********** $tr");
        } else {
            if ($hasDate) {
                fwrite(STDOUT, "$date $msg\n");
            } else {
                fwrite(STDOUT, "$msg\n");
            }
        }
    }

    private function _findModels($status)
    {
        return QueuePostgoods::find()->where(['post_status' => $status])->all();
    }

    public function actionUploadBak()
    {
        echo date("Y-m-d H:i:s").":upload start\n";
        //1、先取出所有的店铺info
        $stores = Store::findStoreInfo();
        $shopids = array();
        foreach ($stores as $key => $store) {
            array_push($shopids,$store['id']);
        }
        //2、再取出这些店铺在处理中的产品，各一条
        $params['status']= $this->_status['dealing'];
        $params['dealingStatus']= $this->_dealing_status['upload'];
        $params['shopIds']= $shopids;
        $products = Product::getDealingGoodsList($params);
        if(!$products){
            echo " no data  "."\n";
            return;
        }
        $productsShopIdMap = ArrayHelper::map($products,"shop_id","id","shop_id");

        $shopIdMap = ArrayHelper::map($stores,"id","platform_id");
        
        foreach ($productsShopIdMap as $key => $shopIdAndProductId) {
            foreach ($shopIdAndProductId as $shopId => $productId) {
                $platform_id = $shopIdMap[$shopId];
                if($platform_id){
                    $platformName =$this->choicePlatForm($platform_id,$productId);
                }
            }
        }       

        foreach ($this->_platformService as $key => $value) {
            $value->actionPubtoamazon($key);
        }
    }

    

    private function choicePlatForm($platform_id,$product_id){
        switch ($platform_id) {
            case '1':
            //组装service数组：key为产品ID，value为对应的service（amazon or ebay）
                $this->_platformService[$product_id] = new AmazonService();
                return "amazon";
                break;
            case '2':
                return "ebay";
                break;
            default:
                echo '没有符合的平台';
                return;
                break;
        }
    }

    //同步在线产品 定时任务入口
    public function actionSync()
    {
        echo date("Y-m-d H:i:s").":sync start\n";
        $param['status'] = $this->_taskStatus['dealing'];
        $tasks = QueueSync::selectByParam($param);
        if(!$tasks){
            echo date("Y-m-d H:i:s") . "no syncOne GetResult data\n";return;
        }
        $amazonService = new AmazonService();
        foreach ($tasks as $key => $task) {
            $amazonService->syncOne($task['id'],$task['shop_id']);
        }
    }

    public function actionSyncTwo()
    {
        echo date("Y-m-d H:i:s").":syncTwo start\n";
        $param['status'] = $this->_taskStatus['request'];
        $tasks = QueueSync::selectByParam($param);
        if(!$tasks){
            echo date("Y-m-d H:i:s") . "no syncTwo GetResult data\n";return;
        }
        $amazonService = new AmazonService();
        foreach ($tasks as $key => $task) {
            $amazonService->syncTwo($task['id'],$task['shop_id']);
        }
    }

    public function actionSyncThree()
    {
        echo date("Y-m-d H:i:s").":syncThree start\n";
        $param['status'] = $this->_taskStatus['reportid'];
        $tasks = QueueSync::selectByParam($param);
        if(!$tasks){
            echo date("Y-m-d H:i:s") . "no syncThree GetResult data\n";return;
        }
        $amazonService = new AmazonService();
        foreach ($tasks as $key => $task) {
            $amazonService->syncThree($task['id'], $task['shop_id'], $task['report_id']);
        }
    }

    public function actionSyncFour(){
        echo date("Y-m-d H:i:s").":syncFour start\n";
        $param = array();
        $tasks = QueueSyncMiddle::selectByParam($param);
        if(!$tasks){
            echo date("Y-m-d H:i:s") . "no syncFour GetResult data\n";return;
        }
        $amazonService = new AmazonService();
        //print_r($tasks);die;
        foreach ($tasks as $key => $task) {
            //try {
                $amazonService->syncFour($task['id'],$task['shop_id'],$task['content'],$task['task_id']);
                //die;
            // } catch (\Exception $e) {
            //     $this->_stdOut($e->getMessage());
            // }
        }

        //$this->_stdOut('syncFour', 'Done');
    }

    //更新在线售卖商品信息（主要是库存 价格 基本信息）
    public function actionUpdate(){
        $this->_stdOut('Update goods', 'start');
        $tasks = QueueUpdategoods::find()->where(['status' => 0])->all();
        if(!$tasks){
            die("No data.\n\n");
        }
        $amazonService = new AmazonService();
        foreach ($tasks as $key => $task) {
            try {
                $amazonService->updateGoodsMain($task);
            } catch (\Exception $e) {
                $this->_stdOut($e->getMessage());
            }
        }

        $this->_stdOut('Update goods', 'Done');
    }


    public function actionDemo(){
        $insertData = $this->actionDemoInsert();
        $dataArray = $this->actionRelationInsert();
        $asinAndPasinData = ArrayHelper::map($dataArray,"ASIN","ParentASIN");
        $realInsertData = array();
        
        foreach ($insertData as $key => $value) {
            $realInsertDataMapByPasin = ArrayHelper::index($realInsertData,"asin1");
            $asin = $value['asin1'];
            if(isset($asinAndPasinData[$asin])){
                $pAsin = $asinAndPasinData[$asin];
                if(empty($pAsin)){
                    //表示这个asin没有组合售卖商品
                    $realInsertData[$key]['asin1'] = $asin;
                    $realInsertData[$key]['item-name'] = $value['item-name'];
                    $realInsertData[$key]['item-description'] = $value['item-description'];
                    $realInsertData[$key]['entry'][] = $value;
                }else{
                    if(empty($realInsertData)){
                        $realInsertData[$key]['asin1'] = $pAsin;
                        $realInsertData[$key]['item-name'] = $value['item-name'];
                        $realInsertData[$key]['item-description'] = $value['item-description'];
                        $realInsertData[$key]['entry'][] = $value;
                        continue;
                    }
                    //表示这个asin的商品 是组合商品其中的一个
                    
                    if(isset($realInsertDataMapByPasin[$pAsin])){
                        $exitPasin = $realInsertDataMapByPasin[$pAsin];
                        if($exitPasin){
                            foreach ($realInsertData as $realKey => $realValue) {
                                if($exitPasin == $realValue){
                                    $realInsertData[$realKey]['entry'][] = $value;
                                    break;
                                }
                            }
                        }else{
                            $realInsertData[$key]['asin1'] = $pAsin;
                            $realInsertData[$key]['item-name'] = $value['item-name'];
                            $realInsertData[$key]['item-description'] = $value['item-description'];
                            $realInsertData[$key]['entry'][] = $value;
                        }
                    }else{
                        $realInsertData[$key]['asin1'] = $pAsin;
                        $realInsertData[$key]['item-name'] = $value['item-name'];
                        $realInsertData[$key]['item-description'] = $value['item-description'];
                        $realInsertData[$key]['entry'][] = $value;
                    }
                }
            }
            
        }
        print_r($realInsertData);
    }

    public function actionRelation(){
        $dataArray = array(
            0 => Array(
                    'ASIN' => "B01HT8XSPO",
                    'ParentASIN' => "",
                ),
            1 => Array(
                    'ASIN' => "B01FE0PDCU",
                    'ParentASIN' => "B01FE0PB7C",
                ),
            2 => Array(
                    'ASIN' => "B01FE0PGL8",
                    'ParentASIN' => "B01FE0PB7C",
                ),
            3 => Array(
                    'ASIN' => "B019Q9FQAE",
                    'ParentASIN' => "B019Q9FNRK",
                ),
            4 => Array(
                    'ASIN' => "B01HS23PDQ",
                    'ParentASIN' => "",
                ),
            5 => Array(
                    'ASIN' => "B01HROLXV6",
                    'ParentASIN' => "B01HRQLRTM",
                ),
            6 => Array(
                    'ASIN' => "B01HROM022",
                    'ParentASIN' => "B01HRQLRTM",
                ),
            7 => Array(
                    'ASIN' => "B01HS0G7LU",
                    'ParentASIN' => "B01HRZ16VM",
                ),
            8 => Array(
                    'ASIN' => "B01HS0G9MM",
                    'ParentASIN' => "B01HRZ16VM",
                ),
            9 => Array(
                    'ASIN' => "B01HEWDPRG",
                    'ParentASIN' => "",
                ),
            10 => Array(
                    'ASIN' => "B01HPYAY9K",
                    'ParentASIN' => "",
                ),
            11 => array(),
            12 => Array(
                    'ASIN' => "B01HPEA9NQ",
                    'ParentASIN' => "",
                ),
        );
        return $dataArray;
    }

    public function actionEcho(){
        $demoArray = Array(
            0 => Array(
                    'item-name' => "mens snapback hat",
                    'item-description' => "best one you never had",
                    'seller-sku' => "B002-m-blue",
                    'price' => "88",
                    'quantity' => "8",
                    'asin1' => "B01HT8XSPO",
                ),
            1 => Array(
                    'item-name' => "Generic PU Leather Unisex Flat Bill Snapback Adjustable",
                    'item-description' => "<b>Suit for boys and girls. Perfect for summer.</b><p> Never Miss </p>",
                    'seller-sku' => "CAP01-Blue",
                    'price' => "102",
                    'quantity' => "6",
                    'asin1' => "B01FE0PDCU",
                ),
            2 => Array(
                    'item-name' => "AAGeneric PU Leather Unisex Flat Bill Snapback Adjustable",
                    'item-description' => "<b>Suit for boys and girls. Perfect for summer.</b><p> Never Miss </p>",
                    'seller-sku' => "CAP01-Grey",
                    'price' => "102",
                    'quantity' => "6",
                    'asin1' => "B01FE0PGL8",
                ),
            3 => Array(
                    'item-name' => "Fanala Women Long Sleeve Zip-up Hoodie Jacket with Zipper Point",
                    'item-description' => "",
                    'seller-sku' => "GM-33",
                    'price' => "33",
                    'quantity' => "3",
                    'asin1' => "B019Q9FQAE",
                ),
            4 => Array(
                    'item-name' => "huaweishouji",
                    'item-description' => "dfsdfdfsd",
                    'seller-sku' => "HW256",
                    'price' => "99",
                    'quantity' => "33",
                    'asin1' => "B01HS23PDQ",
                ),
            5 => Array(
                    'item-name' => "goodtixueshan",
                    'item-description' => "feiyingjihua",
                    'seller-sku' => "TX-008-Black",
                    'price' => "139",
                    'quantity' => "4353",
                    'asin1' => "B01HROLXV6",
                ),
            6 => Array(
                    'item-name' => "goodtixue",
                    'item-description' => "zuihaode qiinggeini",
                    'seller-sku' => "TX-008-Blue",
                    'price' => "101",
                    'quantity' => "4353",
                    'asin1' => "B01HROM022",
                ),
            7 => Array(
                    'item-name' => "windowsxp66",
                    'item-description' => "a good os by weiruan",
                    'seller-sku' => "WIN007-Black-Medium",
                    'price' => "343",
                    'quantity' => "32",
                    'asin1' => "B01HS0G7LU",
                ),
            8 => Array(
                    'item-name' => "windowsxp77",
                    'item-description' => "a good os by weiruan",
                    'seller-sku' => "WIN007-Blue-Medium",
                    'price' => "343",
                    'quantity' => "32",
                    'asin1' => "B01HS0G9MM",
                ),
            9 => Array(
                    'item-name' => "Mens Baseball Hat",
                    'item-description' => "<b>mens hat</b>",
                    'seller-sku' => "b0001-Beige",
                    'price' => "372",
                    'quantity' => "44",
                    'asin1' => "B01HEWDPRG",
                ),
            10 => Array(
                    'item-name' => "Mens Baseball Hat",
                    'item-description' => "<b>mens hat</b>",
                    'seller-sku' => "b0001-Black",
                    'price' => "99",
                    'quantity' => "14",
                    'asin1' => "B01HPYAY9K",
                ),
            11 => Array(
                    'item-name' => "echo test demo on line",
                    'item-description' => "这是一个神奇的产pin",
                    'seller-sku' => "HBK001-white",
                    'price' => "99",
                    'quantity' => "0",
                    'asin1' => "B01HPEA9NQ",
                ),
        );
        return $demoArray;

    }

}
