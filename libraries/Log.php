<?php

namespace app\libraries;

use Yii;

class Log
{

    private $_logFilePath;
    private $_logs = [];
    private $_cacheSize = [];
    private $_cachedChunmSize = 512; // 默认4096(4k)改为1是用于调试
    private $_maxFileSize = 50;
    private $_maxLogFiles = 4;
    private $_rotateByCopy = false;

    private function __construct()
    {
        $this->_logFilePath = Yii::$app->getRuntimePath() . DIRECTORY_SEPARATOR . 'logs/';
    }

    public static function save($msg, $prefix = 'default')
    {
        static $_instance;
        if (is_null($_instance)) {
            $_instance = new self;
        }
        $_instance->_append($msg, $prefix);
    }

    private function _append($msg, $prefix)
    {
        $len = intval(strlen($msg));
        $this->_logs[$prefix][] = [time(), $msg];
        if (isset($this->_cacheSize[$prefix])) {
            $this->_cacheSize[$prefix] += $len;
        } else {
            $this->_cacheSize[$prefix] = $len; 
        }

        if ($this->_cacheSize[$prefix] >= $this->_cachedChunmSize) {
            $this->_flush($prefix);
        }
    }

    private function _flush($prefix = null)
    {
        foreach ($this->_logs as $key => $log) {
            if ($prefix && strcmp($key, $prefix)) {
                continue;
            }
            $this->_writeLogs($key, $log);
            unset($this->_logs[$prefix]);
            unset($this->_cacheSize[$prefix]);
        }
    }

    private function _writeLogs($prefix, $log)
    {
        $fileName = $this->_logFilePath . $prefix . /*date('Y-m-d') . */'.log';

        if (($fp = @fopen($fileName, 'a')) === false) {
            throw new \Exception("Unable to append to log file: $fileName");
        }

        if (@flock($fp, LOCK_EX)) {
            clearstatcache();
            $msg = '';
            foreach ($log as $one) {
                $msg .= date('Y-m-d H:i:s', $one[0]) . "\t{$one[1]}\n";
            }
            if (@filesize($fileName)/1024/1024 >= $this->_maxFileSize) {
                $this->_rotateFiles($fileName);
                flock($fp, LOCK_UN);
                fclose($fp);
                file_put_contents($fileName, $msg, FILE_APPEND | LOCK_EX);
            } else {
                fwrite($fp, $msg);
                flock($fp, LOCK_UN);
                fclose($fp);
            }
        }

    }

    private function _rotateFiles($fileName)
    {
        for ($i = $this->_maxLogFiles; $i >= 0; --$i) {
            $rotateFile = $fileName . ($i === 0 ? '' : '.' . $i);
            if (is_file($rotateFile)) {
                if ($i === $this->_maxLogFiles) {
                    @unlink($rotateFile);
                } else {
                    if ($this->_rotateByCopy) {
                        @copy($rotateFile, $fileName . '.' . ($i + 1));
                        if ($fp = @fopen($rotateFile, 'a')) {
                            @ftruncate($fp, 0);
                            @fclose($fp);
                        }
                    } else {
                        @rename($rotateFile, $fileName . '.' . ($i + 1));
                    }
                }
            }
        }
    }

    public function __destruct()
    {
        $this->_flush();
    }

}