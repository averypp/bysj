<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class BadReview extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sea_bad_review';
    }

}