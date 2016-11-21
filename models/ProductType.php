<?php

namespace app\models;
use Yii;
use yii\web\IdentityInterface;
use yii\db\ActiveRecord;
class ProductType extends ActiveRecord 
	{
	
	

    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sea_product_type';  // '{{user}}';

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

    //copy data  from actneed

    function createProductType($productType_data)
    {
        
        $producttype_model = new ProductType();
        //var_dump($productType_data);die;
        $producttype_model->cat_id = $productType_data['cat_id'];
        $producttype_model->subcat_id = $productType_data['subcat_id'];
        $producttype_model->store_id = $productType_data['store_id'];
        $producttype_model->site_id = $productType_data['site_id'];
        $producttype_model->product_type = isset($productType_data['product_type']) ? $productType_data['product_type']: '';
        $producttype_model->special_upc = isset($productType_data['special_upc']) ? $productType_data['special_upc'] : '';
        $producttype_model->other_value = isset($productType_data['other_value']) ? $productType_data['other_value'] : '';
        $producttype_model->item_type = isset($productType_data['item_type']) ? $productType_data['item_type'] : '';
        $producttype_model->save();
    }

    //check product type info with cat_id ,subcat_id 
    function getProductType($cat_id,$subcat_id){
         $productType_info= ProductType::find()
         ->where(['cat_id' => $cat_id,'subcat_id'=>$subcat_id])
         ->asArray()
         ->one();
        unset($productType_info['id']); unset( $productType_info['cat_id']); unset( $productType_info['subcat_id']); unset( $productType_info['site_id']);
        if($productType_info['special_upc']){
            $productType_info['special_upc']=explode(',', $productType_info['special_upc']);
        }
        if($productType_info['product_type']){
            $productType_info['product_type']=explode(',', $productType_info['product_type']);
        }
        return $productType_info;

    }

}
