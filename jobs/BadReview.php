<?php

namespace app\jobs;

use Yii;
use app\service\SmsService;
use yii\helpers\ArrayHelper;
use app\models\BadReview as BadReviewModel;
use app\models\BadReviewMonitor;
use app\service\BadReviewCollectService;

class BadReview extends BasicWorker
{

    public static function queueName()
    {
        return 'badReview';
    }

    public function perform()
    {
        $this->_now = time();

        $monitorModel = BadReviewMonitor::findOne($this->args['id']);
        if (!$monitorModel) {
            $this->_errorMsg[] = "{$this->args['asin']} not exsits or delete.";
            return;
        }
        // 更新时间
        $monitorModel->update_at = $this->_now;
        // 解锁
        $monitorModel->locked = 0;

        $collectService = new BadReviewCollectService($this->args['asin'], $this->args['last_date'], $this->args['is_first']);
        $datas = $collectService->getMultiDatas();
        // 采集出错，1小时后重试
        if ($datas === false) {
            $monitorModel->last_monitor_at += 3600;
            $monitorModel->save();
            $this->_errorMsg[] = "{$this->args['asin']} ：{$collectService->error}";
            return;
        }
        
        $monitorModel->last_monitor_at = $this->_now;
        if ($datas) {
            if ($this->args['is_first']) {
                $monitorModel->review_total += 1;
                $monitorModel->last_date = $datas[0]['review_date'];
                $datas = array($datas[0]);
            } else {
                // 按日期排序
                ArrayHelper::multisort($datas, 'review_date', SORT_DESC);
                // 设置为未读
                $monitorModel->is_read = 0;
                $monitorModel->review_total += count($datas);
                $monitorModel->last_date = reset($datas)['review_date'];
            }
        }

        if ($monitorModel->save() && $datas) {
            $this->_saveReviewMonitor($datas);
        }
        
    }

    private function _saveReviewMonitor(array $datas)
    {
        foreach ($datas as &$data) {
            $data['create_at'] = $this->_now;
            $data['monitor_id'] = $this->args['id'];
        }
        return Yii::$app->db->createCommand()
        ->batchInsert(BadReviewModel::tableName(), array_keys($datas[0]), $datas)->execute();
    }

}