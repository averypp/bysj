<?php

namespace app\libraries;

use Resque;
use app\models\TaskMonitor;

class Queue
{
    public static function enqueue($class, array $args, $queueName = 'default', $sleepTime = 0, $trackStatus = true)
    {
        if ($class[0] != '\\') {
            $class = '\\' . $class;
        }

        if (substr($class, 0, 9) != '\app\jobs') {
            $class = '\app\jobs' . $class;
        }

        // 延迟执行
        if ($sleepTime > 0) {
            return TaskMonitor::saveTask($class, $args, $queueName, $sleepTime);
        }
    
        Resque::setBackend(\Yii::$app->params['redis']);
        $jobId = Resque::enqueue($queueName, $class, $args, $trackStatus);
        Log::save($jobId . "\tclass[$class]\tqueueName[$queueName]\targs[" . json_encode($args) . "]", 'queue_job');
        return $jobId;
        
    }
}
