<?php

namespace app\commands;

use Yii;
use app\libraries\Queue;
use yii\console\Controller;
use app\models\BadReviewMonitor;
use app\service\SmsService;
use app\models\User;
use app\models\SmsRecord;

class BadReviewController extends Controller
{

    /**
     * 差评监控入口脚本，每10分钟执行一次，用于写入队列
     * 
     * @return void
     */
    public function actionIndex()
    {

        $tasks = BadReviewMonitor::find()
               ->select(['id', 'asin', 'last_monitor_at', 'last_date'])
               ->where(['<', 'last_monitor_at', time() - 86400])
               ->andWhere(['locked' => 0])
               ->limit(1000)->all();

        foreach ($tasks as $task) {
            $args = [
                'id' => $task->id,
                'asin' => $task->asin,
                'last_date' => $task->last_date,
                'is_first' => $task->last_monitor_at > 0 ? false : true,
            ];
            Queue::enqueue('BadReview', $args, 'badReview');
            $task->locked = 1;
            $task->save();
        }
    }

    /**
     * 差评监控短信提醒脚本，每天10点
     * 
     * @return void
     */
    public function actionSms()
    {
        $data = (new \yii\db\Query())
                    ->from(BadReviewMonitor::tableName())
                    ->where(['is_read' => 0])
                    ->select(['SUM(review_total) as num', 'user_id'])
                    ->groupBy('user_id')
                    ->createCommand()
                    ->queryAll();
        $today = date('Y-m-d');
        foreach ($data as $one) {
            if (!$one['num'] || !($user = User::findOne($one['user_id']))) {
                continue;
            }
            // 判断是否已发送过
            $isSend = SmsRecord::find()->where(['send_day' => $today, 'user_id' => $one['user_id'], 'type' => 'bad_review'])->exists();
            if ($isSend) {
                continue;
            }
            // 发送短信
            $msService = new SmsService();
            $ret = $msService->sendForBadReview($user->mobile, $one['num']);
            // 记录短信日志
            $now = time();
            $params = [
                'send_day' => $today,
                'content' => $ret['text'],
                'user_id' => $one['user_id'],
                'mobile' => $user->mobile,
                'create_at' => $now,
                'type' => 'bad_review',
                'status' => $ret['success'] ? 1 : 0,
                'reason' => !$ret['success'] ? $ret['msg'] : '',
            ];
            SmsRecord::saveRecord($params);
            usleep(100);
        }
        
    }

}
