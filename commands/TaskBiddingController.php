<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\Bidding;
use app\libraries\Log;
use app\libraries\Queue;

class TaskBiddingController extends Controller
{

    private $lockedTime = 3600;

    public function actionIndex()
    {
        $lockFile = Yii::$app->getRuntimePath() . '/logs/bidding_lock.txt';
        $fp = @fopen($lockFile, 'a');
        if (!$fp) {
            die('Unable to append to log file: $lockFile');
        }
        // 非阻塞文件锁,保证该脚本执行的内容是单线程
        if (@flock($fp, LOCK_EX | LOCK_NB)) {
            $now = time();
            $tasks = Bidding::find()
                        ->select(['id', 'locked', 'goods_id', 'shop_id', 'sku_id', 'my_price', 'create_at'])
                        ->where(['status' => 1])
                        ->andFilterWhere(['<=', 'locked', $now])
                        ->andFilterWhere(['>', 'rules_id', 0])
                        ->limit(1000)
                        ->all();
            foreach ($tasks as $task) {
                $task->locked = $now + $this->lockedTime;
                if ($task->save()) {
                    Queue::enqueue('GoodsBidding', ['id' => $task->id], 'GoodsBidding');
                }
            }

            @flock($fp, LOCK_UN);
        }
        @fclose($fp);
    }
    
}