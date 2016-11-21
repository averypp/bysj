<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
* 产品的数据层
* add by echo 2016-06-02
*/
class QueuePostgoods extends ActiveRecord 
{
    public static function tableName()
    {
        
        return 'sea_queue_postgoods';
    }

	//获取数据
    public function selectByParam(array $params=array()){
        $postStatus = isset($params['postStatus']) ? $params['postStatus'] : 0;

        $query = QueuePostgoods::find();

        if( is_array($postStatus) ){
        	$query->andWhere(["in","post_status",$postStatus]);
        }else{
        	$query->andWhere(["post_status" => $postStatus]);
        }
        $tasks = $query->asArray()
            ->all();
        return $tasks;
    }

    /**
    * [更新状态]
    * @param $condition 条件参数
    * @set $postStatus 更新的字段值
    *
    */
    public function updatePostStatus($postStatus,array $condition = array() ){
    	$goodsId = isset($condition['goodsId']) ? $condition['goodsId'] : null;
    	$shopId = isset($condition['shopId']) ? $condition['shopId'] : null;
    	$postStatusFrom = isset($condition['postStatus']) ? $condition['postStatus'] : null;
    	$feedSubmissionId = isset($condition['FeedSubmissionId']) ? $condition['FeedSubmissionId'] : null;

    	if(empty($goodsId) || empty($shopId) || (empty($postStatusFrom) && $postStatusFrom!=0) || empty($postStatus)){
    		echo "no update task";
    		return null;
    	}
    	$task = QueuePostgoods::find()
    		->where(["goods_id" => $goodsId,"shop_id" => $shopId,"post_status" => $postStatusFrom])
    		->one();
    	$task->post_status = $postStatus;
    	if($feedSubmissionId){
    		$task->submission_id = $feedSubmissionId;
    	}
    	$task->post_at = time();
    	if($task->update()){
    		echo "update task success";
    	}else{
    		echo "update task error";
    	}
    }
}