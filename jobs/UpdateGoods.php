<?php

namespace app\jobs;

use app\models\AmazonService;

class UpdateGoods extends BasicWorker
{
    public static function queueName()
    {
        return 'updateGoods';
    }

    public function perform()
    {
        $amazonService = new AmazonService();

        try {
            if (!$amazonService->updateGoodsMain($this->args)) {
                throw new \Exception("更新失败: " . json_encode($this->args));
            }
        } catch (\Exception $e) {
            $this->_errorMsg[] = (string)$e;
        }
        
    }
}