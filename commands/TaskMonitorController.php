<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\TaskMonitor;
use app\libraries\Log;
use app\libraries\Queue;

class TaskMonitorController extends Controller
{

    public function actionIndex()
    {

        $lockFile = Yii::$app->getRuntimePath() . '/logs/lock.txt';
        $fp = @fopen($lockFile, 'a');
        if (!$fp) {
            die('Unable to append to log file: $lockFile');
        }

        // 非阻塞文件锁,保证该脚本执行的内容是单线程
        if (@flock($fp, LOCK_EX | LOCK_NB)) {
            $tasks = TaskMonitor::find()->where(['<=', 'begin_at', time()])->all();
            foreach ($tasks as $task) {
                fwrite(STDOUT, "***{$task->class_name}\t{$task->queue_name}\n");
                Queue::enqueue($task->class_name, unserialize($task->args), $task->queue_name);
                $task->delete();
            }
            @flock($fp, LOCK_UN);
        }

        @fclose($fp);
    }
    
}