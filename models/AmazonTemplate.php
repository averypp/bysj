<?php

namespace app\models;
use Yii;
use yii\db\ActiveRecord;
use app\models\GoodsPicture;
use app\models\GoodsSoldInfo;
class AmazonTemplate extends ActiveRecord {
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sea_amazon_feeds_templates';  // '{{user}}';

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
    public static function getTemplateById($tpl_id){
        $template_info= AmazonTemplate::find()
        ->where(['id' => $tpl_id])
        ->asArray()
        ->one();
        return $template_info;
    }

    public static function insertData($siteData, $id = 0){
        if($id){
            $AmazonTemplateModel =  AmazonTemplate::findOne($id);
        } else {
            $AmazonTemplateModel = new AmazonTemplate();
        }
        $AmazonTemplateModel->name = $siteData['name'];
        $AmazonTemplateModel->title = $siteData['title'];
        $AmazonTemplateModel->version = $siteData['version'];
        $AmazonTemplateModel->site_id = $siteData['site_id'];
        if($id){
            return $AmazonTemplateModel->save();
        } else {
            $AmazonTemplateModel->save();
            $insertId = $AmazonTemplateModel->primaryKey;
            return $insertId;
        }
    }

    public static function getData($NewSiteId = 0){
        if($NewSiteId){
            $category_data= AmazonTemplate::find()
            ->where(['site_id' =>$NewSiteId])
             ->asArray()
             ->all();
        } else {
            $category_data= AmazonTemplate::find()
             ->asArray()
             ->all();
        }
         if($category_data){
            return $category_data;
         } else {
            return false;
         }
    }

    public static function deleteData($id){
        AmazonTemplate::deleteAll('id = :id', [':id' => $id]);
    }
    public static function findIdByNameAndSiteId($siteId, $name){
        $data = AmazonTemplate::find()
            ->where(['site_id'=>$siteId, 'name' =>$name])
            ->asArray()
            ->one();
        return $data['id'];
    }

    
}
