<?php

namespace app\models;
use Yii;
use yii\db\ActiveRecord;
class AmazonFeedValues extends ActiveRecord {
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sea_amazon_feed_values';  // '{{user}}';

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
     *查询变体信息（actneed 的productType项目）
     */
    public static function getProducttype($tpl_id ,$site_id){
    	$Table = AmazonFeedValues::tableName();
    	$query = (new \yii\db\Query())
    	->select(['type'])
    	->from($Table)
    	->where(['site_id' => $site_id ,'tpl_id' => $tpl_id, 'field' => 'variation_theme']);
    	
    	$variation_data = $query->all();
    	$product_type = [];
    	if($variation_data){
    		foreach ($variation_data as $key => $value){
                if(strlen($value['type'])){
                    $product_type[] = $value['type'];
                }
    		}
    	}
    	return $product_type;
    }
    
    /**
     *查询变体的值，size color color-size。。。。。
     */
    public static function getVariationThemes($tpl_id ,$site_id ,$type){
        $Table = AmazonFeedValues::tableName();
        $query = (new \yii\db\Query())
        ->select(['values'])
        ->from($Table)
        ->where(['site_id' => $site_id ,'tpl_id' => $tpl_id, 'field' => 'variation_theme' ,'type' => $type]);
       
        $variationThemes_data = $query->one();

        $variationThemes_array = [];
    	if($variationThemes_data){
    		$variationThemes_array = explode('|' ,$variationThemes_data['values']);
    	}
        return $variationThemes_array;
    }

    /**
    *get Variation Values
    */
    public function getFieldValues($tpl_id, $site_id, $field, $type=''){

        $Table = AmazonFeedValues::tableName();
        $where = ['site_id' => $site_id, 'tpl_id' => $tpl_id, 'field' => $field];
        if($type){
            $where['type'] = $type;
        }
        $query = (new \yii\db\Query())
        ->select(['values'])
        ->from($Table)
        ->where($where);
        $values = $query->one();
        // echo $query->createCommand()->getRawSql();

        $values_arr = [];
        if($values){
            $values_arr = explode('|' ,$values['values']);
        }
        return $values_arr;
    }

    public static function insertData($siteData, $tplId){
        $AmazonFeedValuesModel = new AmazonFeedValues();
        $AmazonFeedValuesModel->field = $siteData['field'];
        $AmazonFeedValuesModel->label = $siteData['label'];
        $AmazonFeedValuesModel->type = $siteData['type'];
        $AmazonFeedValuesModel->values = $siteData['values'];
        $AmazonFeedValuesModel->tpl_id = $tplId;
        $AmazonFeedValuesModel->site_id = $siteData['site_id'];
        return $AmazonFeedValuesModel->save();
    }


    public static function deleteData($siteId, $tplId){
        AmazonFeedValues::deleteAll('site_id = :site_id AND tpl_id = :tpl_id', [':site_id' => $siteId, ':tpl_id' => $tplId]);
        
    }

}
