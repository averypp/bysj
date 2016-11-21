<?php

namespace app\jobs;

use app\libraries\Log;
use app\models\TaskMonitor;

abstract class BasicWorker
{

    protected $_errorMsg = [];
    protected $_isRetry = false;
    protected $_sleepTime = 60;

    public function setUp()
    {
        $this->_saveLog("***Start:***" . (string)$this->job);
    }

    public function perform()
    {
        $method = array_shift($this->args);

        if (!method_exists($this, $method)) {
            throw new \Exception("class method $method not exists.");
        }

        call_user_func_array(array($this, $method), $this->args);
    }

    public function tearDown()
    {

        if ($this->_isRetry) {
            // 执行失败允许重复的次数
            if (isset($this->args['retryTimes']) && $this->args['retryTimes'] > 0) {
                $class = $this->job->payload['class'];
                $args = $this->job->payload['args'][0];
                $args['retryTimes']--;
                if (isset($this->args['sleepTime']) && $this->args['sleepTime'] > 0) {
                    $this->_sleepTime = (int)$this->args['sleepTime'];
                }
                TaskMonitor::saveTask($class, $args, $this->queue, $this->_sleepTime);
            }
        }

        // 记录日志
        if ($this->_errorMsg) {
            $this->_saveLog($this->_errorMsg);
        }
        $this->_saveLog("***Done.***\n");
    }

    protected function _saveLog($msg)
    {
        if (is_array($msg)) {
            $msg = implode("\n", $msg);
        }
        Log::save($msg, $this->queue);
    }

    public static function queueName()
    {
        # return '队列名称';
    }

}