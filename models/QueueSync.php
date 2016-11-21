<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class QueueSync extends ActiveRecord
{
    public static function tableName()
    {
        return 'sea_queue_sync';

    }

    public function setFields(array $fields)
    {
        foreach ($fields as $key => $value) {
            $this->$key = $value;
        }
    }

    public function getLastSyncData($shopId, $fields = null)
    {
        $ret = QueueSync::find()->where(['shop_id' => $shopId])->orderBy('gmt_create desc')->limit(1)->one();
        if (!$ret) {
            return;
        }

        if (is_null($fields)) {
            return $ret;
            
        }

        if (strpos($fields, ',') !== false) {
            $fields = explode(',', $fields);
        } else {
            settype($fields, 'array');
        }
        $r = [];
        foreach ($fields as $field) {
            $field = trim($field);
            if ($field) {
                $r[$field] = $ret->$field;
            }
        }

        return $r;
    }

    public function saveData($shopId)
    {
        $gmt = date('Y-m-d H:i:s');
        $fields = [
            'shop_id' => $shopId,
            'gmt_create' => $gmt,
            'gmt_modified' => $gmt,
        ];

        $this->setFields($fields);

        return $this->insert();
    }

    public function selectByParam($queryParam){
        $status = isset($queryParam['status']) ? $queryParam : 0;

        $ret = QueueSync::find()
        ->andWhere(["status" => $status])
        ->all();
        // die($ret->createCommand()->getRawSql());
        return $ret;

    }

    public function syncComplete($shop_id, $status, $reportId = ''){
        $queueSync = QueueSync::find()->andWhere(["shop_id" => $shop_id])->one();
        if(empty($queueSync)){
            return null;
        }
        $_time = time();
        $queueSync->status = $status;
        $queueSync->sync_at = $_time;
        $queueSync->report_id = $reportId;
        $queueSync->gmt_modified = date("Y-m-d H:i:s");
        return $queueSync->update();
    }

    public function syncComplete_back($id, $status, $reportId = ''){
        $queueSync = QueueSync::find()->andWhere(["id" => $id])->one();
        if(empty($queueSync)){
            return null;
        }
        if( $status == 4 ){
            return $queueSync->delete();
        }
        $_time = time();
        $queueSync->status = $status;
        $queueSync->sync_at = $_time;
        $queueSync->report_id = $reportId;
        $queueSync->gmt_modified = date("Y-m-d H:i:s");
        return $queueSync->update();
    }

    public function syncDelete($shop_id){
        $queueSync = QueueSync::find()->andWhere(["shop_id" => $shop_id])->one();
        return $queueSync->delete();
    }

}