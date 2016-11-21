<?php 

namespace app\models;
use Yii;
use yii\db\ActiveRecord;

/**
* 注册发送验证码记录 的数据层
* add by echo 2016-06-17
*/
class AmazonFeeds extends \yii\db\ActiveRecord 
{
	public static function tableName()
    {
        return 'sea_amazon_feeds';
    }

    public static function getFeedsByGoodId($good_id){
        $feeds_info= AmazonFeeds::find()
        ->where(['good_id' => $good_id])
        ->asArray()
        ->one();
        return $feeds_info;
    }

    public function getTplData()
    {
        return $this->hasMany(AmazonFeedTplData::className(), ['tpl_id' => 'tpl_id']);
    }

    public static function fieldMessage()
    {
        return [
            'item_sku' => '产品的SKU不能为空!',
            'external_product_id' => '产品编码不足，请为产品设置相应的产品编码！',
            'external_product_id_type' => '产品没有设置相应的产品编码类型！',
            'item_name' => '产品名称不能为空',
            'brand_name' => '产品的Brand不能为空',
            'manufacturer' => '产品的Manufacture不能为空',
            'item_type' => '产品没有选择产品类别!',
            'feed_product_type' => '产品没有选择相应分类的product type！',
            'color_name' => '产品没有设置颜色',
            'department_name' => '没有department_name参数',
            'size_name' => '产品没有设置尺寸',
        ];
    }
   
}


?>
