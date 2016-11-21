<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class QueueUpdategoods extends ActiveRecord
{
    public static function tableName()
    {
        return 'sea_queue_updategoods';

    }

    public function setFields(array $fields)
    {
        foreach ($fields as $key => $value) {
            $this->$key = $value;
        }
    }

    public function selectByParam($queryParam){
        $status = isset($queryParam['status']) ? $queryParam : 0;

        $ret = QueueUpdategoods::find()
        ->andWhere(["status" => $status])
        ->all();
        // die($ret->createCommand()->getRawSql());
        return $ret;

    }

    public function updateGoodsSuccess($id){
        $queueUpdateGoods = QueueUpdategoods::find()->andWhere(["id" => $id])->one();
        if(empty($queueUpdateGoods)){
            return null;
        }
        $_time = time();
        $queueUpdateGoods->status = 1;
        $queueUpdateGoods->gmt_modified = date("Y-m-d H:i:s");
        return $queueUpdateGoods->update();
    }

}