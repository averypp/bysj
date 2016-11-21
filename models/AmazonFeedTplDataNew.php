<?php

namespace app\models;
use Yii;
use yii\db\ActiveRecord;
use app\models\AmazonFeedValues;
class AmazonFeedTplDataNew extends ActiveRecord {
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sea_amazon_feed_tpl_data_new';  // '{{user}}';

    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
           /* [['id', 'is_deleted', 'reg_time'], 'required'],
             [['dress', 'sale_status', 'unit_price', 'status', 'special', 'special_time', 'hot', 'holiday', 'add_time', 'update_time'], 'integer'],
            [['summary'], 'string'],
            [['house_name', 'trait'], 'string', 'max' => 100],
            [['amount', 'promotion', 'image', 'logo', 'video'], 'string', 'max' => 255],
            [['price', 'mobile', 'special_price'], 'string', 'max' => 20],
            [['interface_city'], 'string', 'max' => 50],
            [['city_id', 'district_id'], 'string', 'max' => 8],
            [['address', 'start_date', 'give_date'], 'string', 'max' => 150],
            [['contact', 'tele'], 'string', 'max' => 30],
            [['map'], 'string', 'max' => 60],*/
        ];
    }


    public static function getData($siteIdNew, $newTplId){
        if($siteIdNew && $newTplId){
            $category_data= AmazonFeedTplDataNew::find()
             ->where(['tpl_id' =>$newTplId, 'site_id'=> $siteIdNew])
             ->asArray()
             ->all();
            return $category_data;
        } else {
            return false;
        }
    }


}
