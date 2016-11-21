<?php

namespace app\models;
use Yii;
use yii\web\IdentityInterface;
use yii\db\ActiveRecord;
class GoodsSoldInfo extends ActiveRecord
	{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sea_goods_sku';  // '{{user}}';

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

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            // 'id' => 'ID',
            //'username' => 'Username',
           // 'password' => 'password',
        	//'mobile'   => 'mobile',
           // 'teg_time' => 'teg_time',
        ];
    }

    /**
     * [根据 产品主表ID查询 售卖信息]
     * @param  string      $id
     * @return array or null
     */
    public  static function findByGoodsId($goodsId)
    {
          $goodsSoldInfoModel = new GoodsSoldInfo();
          $goodsSoldInfo = $goodsSoldInfoModel::find()
            ->where(['goods_id' => $goodsId , 'is_deleted' => "N"])
            ->asArray()
            ->all();
            if($goodsSoldInfo){
                return $goodsSoldInfo;
            }
        return null;
    }

    public function getSpecs()
    {
        return $this->hasMany(GoodsSpec::className(), ['sku_id' => 'id']);
    }
    public function getPic()
    {
        return $this->hasMany(GoodsPicture::className(), ['sku_id' => 'id']);
    }

}
