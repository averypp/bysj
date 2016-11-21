<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class QueueBadreviewSms extends ActiveRecord 
{
    public static function tableName()
    {
        return 'sea_queue_badreview_sms';
    }
}
    