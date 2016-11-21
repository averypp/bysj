<?php

namespace app\jobs;

use app\models\AmazonService;
use app\libraries\Log;
use app\libraries\Queue;
use app\models\AmazonFeeds;
use app\models\Product;
use app\models\TaskMonitor;

class PublishGoods extends BasicWorker
{

    private $_amazonService;

    public static function queueName()
    {
        return 'publishGoods_one,publishGoods_two';
    }

    public function setUp()
    {
        parent::setUp();
        $this->_amazonService = new AmazonService();
    }

    public function publishGoods($args)
    {
        try {
            $feedSubmissionId = $this->_amazonService->actionPubtoamazon($args['goods_id'], $args['shop_id']);

            // 产品上传提交亚马逊成功，则插入获取处理结果队列
            $args = [
                'getPublishResult',
                [
                    'goods_id' => $args['goods_id'],
                    'shop_id' => $args['shop_id'],
                    'submission_id' => $feedSubmissionId,
                ],
                'retryTimes' => 1,
            ];
            Queue::enqueue('PublishGoods', $args, 'publishGoods_two', 120);

        } catch (\Exception $e) {
            $this->_errorMsg[] = (string)$e;
            $goods = Product::findOne($args['goods_id']);
            $goods->pub_status = 0;
            $goods->dealing_status = null;
            $goods->save();
        }
        
    }

    public function getPublishResult($args)
    {
        $customer = AmazonFeeds::findOne(['good_id' => $args['goods_id']]);
        $customer->status = 'processed';
        
        try {
            $res = $this->_amazonService->getSubmissionResult($args['goods_id'], $args['shop_id'], $args['submission_id']);

            // 成功获取结果则更新状态
            if (!$res) {
                $pubStatus = 4;
                $customer->success = 'success';
            } else {
                $customer->results = implode(" ", $res);
                $customer->success = 'error';
                $pubStatus = 3;
            }
            $customer->save();
            Product::updatePubStatus($args['goods_id'], $pubStatus);
           
        } catch (\Exception $e) {
            $this->_errorMsg[] = (string)$e;
            $customer->success = 'error';
            $customer->results = $e->getMessage();
            $customer->save();
            Product::updatePubStatus($args['goods_id'], 3);
        }
    }

}