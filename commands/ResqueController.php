<?php

namespace app\commands;

use yii\console\Controller;
use yii\helpers\ArrayHelper;

class ResqueController extends Controller
{

    public $QUEUE;
    public $LOGGING;
    public $VERBOSE;
    public $VVERBOSE;
    public $INTERVAL;
    public $APP_INCLUDE;
    public $REDIS_BACKEND;
    public $COUNT;
    public $PIDFILE;

    public function options()
    {
        return [

            // 需要执行的队列的名字
            'QUEUE', 

            // 启用日志级别
            'LOGGING',

            // 啰嗦模式，设置“1”为启用，会输出基本的调试信息
            'VERBOSE',

            // 设置“1”启用更啰嗦模式，会输出详细的调试信息
            'VVERBOSE',

            // 在队列中循环的间隔时间，即完成一个任务后的等待时间，默认是5秒
            'INTERVAL', 

            // 需要自动载入PHP文件路径，Worker需要知道你的Job的位置并载入Job
            'APP_INCLUDE', 

            // redis服务器的地址，使用hostname:port的格式，
            // 如127.0.0.1:6379，或localhost:6379。默认是localhost:6379
            'REDIS_BACKEND',

            // 需要创建的Worker的数量。所有的Worker都具有相同的属性。默认是创建1个Worker
            'COUNT', 

            // 手动指定PID文件的位置，适用于单Worker运行方式
            'PIDFILE',
        ];
    }

    public function optionAliases()
    {
        return [
            'q' => 'QUEUE',
            'l' => 'LOGGING',
            'v' => 'VERBOSE',
            'vv' => 'VVERBOSE',
            'i' => 'INTERVAL',
            'a' => 'APP_INCLUDE',
            'r' => 'REDIS_BACKEND',
            'c' => 'COUNT',
            'p' => 'PIDFILE',
        ];
    }

    public function actionIndex()
    {
        if(empty($this->QUEUE)) {
            die("Set QUEUE env var containing the list of queues to work.\n");
        }

        if(!empty($this->REDIS_BACKEND)) {
            \Resque::setBackend($this->REDIS_BACKEND);
        } else {
            \Resque::setBackend(\Yii::$app->params['redis']);
        }

        $logLevel = 0;
        if(!empty($this->LOGGING) || !empty($this->VERBOSE)) {
            $logLevel = \Resque_Worker::LOG_NORMAL;
        }
        else if(!empty($this->VVERBOSE)) {
            $logLevel = \Resque_Worker::LOG_VERBOSE;
        }

        if($this->APP_INCLUDE) {
            if(!file_exists($this->APP_INCLUDE)) {
                die('APP_INCLUDE ('.$this->APP_INCLUDE.") does not exist.\n");
            }
            require_once $this->APP_INCLUDE;
        }

        $interval = 5;
        if(!empty($this->INTERVAL)) {
            $interval = $this->INTERVAL;
        }

        $count = 1;
        if(!empty($this->COUNT) && $this->COUNT > 1) {
            $count = $this->COUNT;
        }

        if ($count > 1) {
            for($i = 0; $i < $count; ++$i) {
                $pid = pcntl_fork();
                if($pid == -1) {
                    die("Could not fork worker ".$i."\n");
                }
                // Child, start the worker
                else if(!$pid) {
                    $queues = explode(',', $this->QUEUE);
                    $worker = new \Resque_Worker($queues);
                    $worker->logLevel = $logLevel;
                    fwrite(STDOUT, '*** Starting worker '.$worker."\n");
                    $worker->work($interval);
                    break;
                }
            }
        } else { // Start a single worker
            $queues = explode(',', $this->QUEUE);
            $worker = new \Resque_Worker($queues);
            $worker->logLevel = $logLevel;
            
            if ($this->PIDFILE) {
                file_put_contents($this->PIDFILE, getmypid()) or
                    die('Could not write PID information to ' . $this->PIDFILE);
            }

            fwrite(STDOUT, '*** Starting worker '.$worker."\n");
            $worker->work($interval);
        }
    }

    public function actionCheckStatus($jobId)
    {

        \Resque::setBackend(\Yii::$app->params['redis']);

         $status = new \Resque_Job_Status($jobId);
         if(!$status->isTracking()) {
            die("Resque is not tracking the status of this job.\n");
        }

        fwrite(STDOUT, "Tracking status of ".$jobId.". Press [break] to stop.\n\n");
        while (true) {
            fwrite(STDOUT, "Status of ".$jobId." is: ".$status->get()."\n");
            sleep(3);
        }
    }

    public function actionError($count = 10, $jobId = null)
    {
        \Resque::setBackend(\Yii::$app->params['redis']);
        $errors = \Resque::redis()->lrange('failed', 0, $count);
        foreach ($errors as &$error) {
            $error = json_decode($error, 1);
            $error['id'] = $error['payload']['id'];
            unset($error['payload'], $error['exception']);
            $error['failed_at'] = date('Y-m-d H:i:s', strtotime($error['failed_at']) + 3600*8);
            $error['backtrace'] = implode("\n", $error['backtrace']);
        }
        $errors = ArrayHelper::index($errors, 'id');
        if (!empty($jobId)) {
            if (!isset($errors[$jobId])) {
                die("jobId:$jobId not exists.\n");
            }
            die(implode("\n", $errors[$jobId]));
        }

        foreach ($errors as &$one) {
            $one = implode("\n", $one);
            $one .= "\n";
        }
        var_dump(implode("\n", $errors));
    }

}