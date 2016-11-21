<?php


namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class SmsRecord extends ActiveRecord 
{
    public static function tableName()
    {
        return 'sea_sms_record';
    }

    public static function saveRecord(array $params)
    {
        if (!$params) {
            return false;
        }
        $model = new SmsRecord();
        foreach ($params as $key => $value) {
            $model->$key = $value;
        }
        return $model->insert();
    }
}