<?php

namespace app\models;
use Yii;
use yii\db\ActiveRecord;
use app\models\AmazonFeedValues;
class AmazonFeedTplData extends ActiveRecord {
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sea_amazon_feed_tpl_data';  // '{{user}}';

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
    public function getTplColumn(){
        //$items = AmazonFeedTplData::find(array(
       //   'select'=>array('field'),
       //   'condition' => 'tpl_id=:tpl_id AND site_id=:site_id',
       //   'params' => array(':tpl_id'=>'1',':site_id' => '29'),
      //  ));


        $items = AmazonFeedTplData::findAll(array(
          'select' =>array('field'),
        ));
        //var_dump($items);die;
        return $items;
    }
    
	public function getRequiredFieldWithValues($tpl_id ,$site_id){
      $Table = AmazonFeedTplData::tableName();
    	$query = (new \yii\db\Query())
    	->select(['label'])
    	->from($Table)
    	->where(['site_id' => $site_id ,'tpl_id' => $tpl_id,'required' => 'Required']);
    	$required_field = $query->all();
    	$filter_field = ['SKU', 'Seller SKU', 'Title' , 'Product ID' , 'Product ID Type', 'Manufacturer' , 'Item Type' ,'Brand'];
    	foreach ($required_field as $key => $value){
    		if(in_array($value['label'],$filter_field)){
    			unset($required_field[$key]);
    		}
    	}
      //\Yii::info("shb".print_r($required_field, true));
    	$required_attr = [];
    	if($required_field){
	    	foreach ($required_field as $rkey => $rvalue){
	    		// if($rvalue['label'] == 'Size'){
	    		// 	$required_attr['Size'] = $this->getRequiredValue($tpl_id ,$site_id ,'size_map');
	    		// } else if ($rvalue['label'] == 'Color'){
	    		// 	$required_attr['Color'] = $this->getRequiredValue($tpl_id ,$site_id ,'color_map');
	    		// }else{
	    			$required_attr[ str_replace(" ", '', trim($rvalue['label'])) ] = $this->getRequiredValue($tpl_id ,$site_id ,$rvalue['label']);
	    		// }
	    	}
	    	/*foreach ($required_field as $rkey => $rvalue){
	    		if($rvalue['field'] == 'color'){
	    			break;
	    		}else {
	    			$required_attr['Color'] = $this->getRequiredValue(2 ,29 ,'color_map');
	    		} 
	    	}*/
    	}
      return $required_attr;
    	
    }
    
    public function getAllFields($tpl_id ,$site_id){
      $Table = AmazonFeedTplData::tableName();
      $query = (new \yii\db\Query())
      ->select(['label', 'field'])
      ->from($Table)
      ->where(['site_id' => $site_id ,'tpl_id' => $tpl_id]);
      $all_fields = $query->all();
      
      $fields = [];
      if($all_fields){
        foreach ($all_fields as $rkey => $rvalue){
            $fields[ str_replace(" ", '', trim($rvalue['label'])) ] = $rvalue['field'];
        }
      }
      return $fields;
    }

    public function getRequiredField($tpl_id, $site_id, $type=""){
      $Table = AmazonFeedTplData::tableName();
      $query = (new \yii\db\Query())
      ->select(['label', 'field'])
      ->from($Table)
      ->where(['site_id' => $site_id ,'tpl_id' => $tpl_id,'required' => 'Required']);
      $required_field = $query->all();
      $filter_field = ['item_sku', 'item_name', 'manufacturer', 'item_type', 'brand_name', 'external_product_id', 'external_product_id_type', 'main_image_url', 'standard_price', 'quantity'];//filter_field
      // $filter_field = ['item_sku', 'item_name', 'manufacturer', 'item_type', 'brand_name', 'external_product_id', 'external_product_id_type', 'main_image_url', 'standard_price', 'part_number', 'quantity', 'model'];//filter_field
      if($type){
        $filter_field[] = 'feed_product_type';
      }
      $fields = [];
      foreach ($required_field as $key => $value){
        if( !in_array($value['field'], $filter_field) ){
          $fields[ str_replace(" ", '', trim($value['label'])) ] = $value['field'];
        }
      }

      return $fields;
    }

    public function getRequiredValue($tpl_id, $site_id, $label, $type=''){
    	$Table = AmazonFeedValues::tableName();
      $where = ['site_id' => $site_id, 'tpl_id' => $tpl_id, 'label' => $label];
        if($type){
            $where['type'] = $type;
        }
    	$query = (new \yii\db\Query())
    	->select(['values'])
    	->from($Table)
    	->where($where);
    	$required_values = $query->one();
    	return explode('|' ,$required_values['values']);
    }
    public static function getFieldLableArray($tpl_id){
      $Table = AmazonFeedTplData::tableName();
      $query = (new \yii\db\Query())
      ->select(['label', 'field'])
      ->from($Table)
      ->where(['tpl_id' => $tpl_id]);
      $all_fields = $query->all();
      $fields = [];
      if($all_fields){
        foreach ($all_fields as $rkey => $rvalue){
            $fields[ str_replace(" ", '', trim($rvalue['label'])) ] = $rvalue['field'];
        }
      }
      return $fields;
    }


    public static function insertData($siteData, $tplId){
        $AmazonFeedTplDataModel = new AmazonFeedTplData();
        $AmazonFeedTplDataModel->field = $siteData['field'];
        $AmazonFeedTplDataModel->label = $siteData['label'];
        $AmazonFeedTplDataModel->definition = $siteData['definition'];
        $AmazonFeedTplDataModel->accepted = $siteData['accepted'];
        $AmazonFeedTplDataModel->example = $siteData['example'];
        $AmazonFeedTplDataModel->group_id = $siteData['group_id'];
        $AmazonFeedTplDataModel->required = $siteData['required'];
        $AmazonFeedTplDataModel->group = $siteData['group'];
        $AmazonFeedTplDataModel->tpl_id = $tplId;
        $AmazonFeedTplDataModel->site_id = $siteData['site_id'];
        return $AmazonFeedTplDataModel->save();
    }

    public static function deleteData($siteId, $tplId){
        AmazonFeedTplData::deleteAll('site_id = :site_id AND tpl_id = :tpl_id', [':site_id' => $siteId, ':tpl_id' => $tplId]);
    }

}
