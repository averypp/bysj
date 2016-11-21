<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class TaskMonitor extends ActiveRecord
{

    public static function tableName()
    {
        return 'sea_task_monitor';
    }

    public static function saveTask($className, array $args, $queueName, $sleepTime = 60)
    {
        $_now = time();
        $taskMonitor = new TaskMonitor();
        $taskMonitor->class_name = $className;
        $taskMonitor->args = serialize($args);
        $taskMonitor->queue_name = $queueName;
        $taskMonitor->create_at = $_now;
        $taskMonitor->sleep_time = $sleepTime;
        $taskMonitor->begin_at = $sleepTime + $_now;
        return $taskMonitor->save();
    }

}
