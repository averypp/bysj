<?php

namespace app\jobs;

use app\models\AmazonService;
use app\models\QueueSync;
use app\models\QueueSyncMiddle;
use app\libraries\Log;
use app\libraries\Queue;
use app\models\TaskMonitor;

class SyncGoods extends BasicWorker
{
    private $_amazonService;

    public static function queueName()
    {
        return 'syncGoods_one,syncGoods_two,syncGoods_three,syncGoods_four';
    }

    public function setUp()
    {
        parent::setUp();
        $this->_amazonService = new AmazonService();
    }

    public function stepOne($args)
    {

        // var_dump('this one');
        // var_dump($args);
        try {
            $result = $this->_amazonService->syncOne($args['shop_id']);
            if($result){
                echo 'Sync Online RequestReport success. ShopID: '.$args['shop_id']."\n";
                // $process = QueueSync::syncComplete($args['id'], 1);

                $args = [
                    'stepTwo',
                    [
                        // 'id' => $args['id'],
                        'shop_id' => $args['shop_id']
                    ],
                ];
                Queue::enqueue('SyncGoods', $args, 'syncGoods_two', 10);
            }else{
                $this->_errorMsg[] = 'Sync Online RequestReport failed. ShopID: '.$args['shop_id'];
                $process = QueueSync::syncComplete($args['shop_id'], 1);
            }
        } catch (\Exception $e) {
            $this->_errorMsg[] = (string)$e;
        }
    }

    public function stepTwo($args)
    {
        // var_dump('this two');
        // var_dump($args);
        try {
            $generatedReportId = $this->_amazonService->syncTwo(/*$args['id'], */$args['shop_id']);
            if($generatedReportId){
                echo 'getReportID success generatedReportId: '.$generatedReportId."\n";
                // $process = QueueSync::syncComplete($id, 2, $generatedReportId);

                $args = [
                    'stepThree',
                    [
                        // 'id' => $args['id'],
                        'shop_id' => $args['shop_id'],
                        'report_id' => $generatedReportId
                    ],
                ];
                Queue::enqueue('SyncGoods', $args, 'syncGoods_three');
            }else{
                $this->_errorMsg[] = "同步数据异常";
                $process = QueueSync::syncComplete($args['shop_id'], 2);
            }
        } catch (\Exception $e) {
            $this->_errorMsg[] = (string)$e;
        }
    }

    public function stepThree($args)
    {
        // var_dump('this three');
        // var_dump($args);
        try {
            $result = $this->_amazonService->syncThree(/*$args['id'], */$args['shop_id'], $args['report_id']);
            if($result){
                foreach ($result as $key => $value) {
                    // $saveData['task_id'] = $args['id']; 
                    $saveData['shop_id'] = $args['shop_id']; 
                    $saveData['content'] = base64_encode(serialize(array_map('utf8_encode', $value)));
                    $saveData['status'] = 0;
                    $saveData['gmt_create'] = date("Y-m-d H:i:s");
                    $saveData['gmt_modified'] = $saveData['gmt_create'];
                    $saveResult = QueueSyncMiddle::saveData($saveData);
                }

                echo "同步线上数据存到中间表 ok\n";
                // $process =  QueueSync::syncComplete($args['id'], 3, $args['report_id']);

                $args = [
                    'stepFour',
                    [
                        // 'id' => $args['id'],
                        'shop_id' => $args['shop_id']
                    ],
                ];
                Queue::enqueue('SyncGoods', $args, 'syncGoods_four', 30);
            }else{
                $this->_errorMsg[] = "同步线上数据存到中间表之获取线上数据异常";
                $process = QueueSync::syncComplete($args['shop_id'], 3);
            }
        } catch (\Exception $e) {
            $this->_errorMsg[] = (string)$e;
        }
    }

    public function stepFour($args)
    {
        // var_dump('this four');
        // var_dump($args);
        try {
            $tasks = QueueSyncMiddle::selectByShopId($args['shop_id']);
            if($tasks){
                foreach ($tasks as $key => $task) {
                    $saveResult = $this->_amazonService->syncFour($task['shop_id'],$task['content']);
                    if($saveResult){
                        $result = "中间表任务完成";
                    }else{
                        $result = "中间表任务异常";
                    }
                    if(QueueSyncMiddle::syncComplete($task['id'])){
                        echo $result.",记录删除\n";
                        // $process = QueueSync::syncComplete($task['task_id'], 4);
                    }else{
                        $this->_errorMsg[] = "中间表数据异常";
                    }
                }
            }else{
                $this->_errorMsg[] = date("Y-m-d H:i:s") . "no syncFour GetResult data\n";
                // die();
                // $process = QueueSync::syncDelete($args['id']);
            }
            $process = QueueSync::syncComplete($args['shop_id'], 4);
        } catch (\Exception $e) {
            $this->_errorMsg[] = (string)$e;
        }
    }

}