<?php 

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class QueueSyncMiddle extends ActiveRecord{
	
	public static function tableName()
    {
        return 'sea_queue_sync_middle';

    }

    public function saveData($saveData){
    	$syncMiddle = new QueueSyncMiddle();
    	$syncMiddle->shop_id = $saveData['shop_id'];
    	// $syncMiddle->task_id = $saveData['task_id'];
    	$syncMiddle->content = $saveData['content'];
    	$syncMiddle->status = 0;
    	$syncMiddle->gmt_create = $saveData['gmt_create'];
    	$syncMiddle->gmt_modified = $saveData['gmt_modified'];

    	if($syncMiddle->save()){
            $id = $syncMiddle->attributes['id'];//数据保存后返回插入的ID
            $syncMiddle->id = $id;
            return $syncMiddle;
        }
        return null;

    }

    public function selectByParam($queryParam){
        $status = isset($queryParam['status']) ? $queryParam : 0;

        $ret = QueueSyncMiddle::find()
        ->andWhere(["status" => $status])
        ->orderBy('gmt_modified asc')
        ->all();
        // die($ret->createCommand()->getRawSql());
        return $ret;

    }

    public function selectByShopId($shop_id){
        $ret = QueueSyncMiddle::find()
        ->andWhere(["shop_id" => $shop_id])
        ->orderBy('gmt_modified asc')
        ->all();
        // die($ret->createCommand()->getRawSql());
        return $ret;

    }

    public function syncComplete($id){
        $count = QueueSyncMiddle::findOne($id)->delete();
        if( $count ){
            return true; 
        }else{
            return false; 
        }
		// $queueSync = QueueSyncMiddle::find()->andWhere(["id" => $id])->one();
  //       if(empty($queueSync)){
  //           return null;
  //       }
  //       $queueSync->status = 1;
  //       $queueSync->gmt_modified = date("Y-m-d H:i:s");
  //       return $queueSync->update();
    }
}

?>