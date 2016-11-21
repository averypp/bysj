<?php

namespace app\models;
use Yii;
use yii\db\ActiveRecord;
use app\models\GoodsPicture;
use app\models\GoodsSoldInfo;
class AmazonBtg extends ActiveRecord {
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sea_amazon_btg';  // '{{user}}';

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
    * @获取子分类信息
    * @return 子分类信息
    */
    public static function getCategory($site_id ,$parent_id = 0, $tpl_id){
        if($parent_id == 0){
            $category_data= AmazonBtg::find()
             ->where(['site_id' => $site_id ,'parent_id' => $parent_id])
             ->asArray()
             ->all();
        } else{
            $category_data= AmazonBtg::find()
             ->where(['site_id' => $site_id, 'parent_id' => $parent_id, 'tpl_id' =>$tpl_id])
             ->asArray()
             ->all();
        }
        return $category_data;
    }


    /**
    * @param node_id level
    * @return 
    */
    public static function getBtgData($child_id, $levelId ,$tpl_id){
        $btg_data= AmazonBtg::find()
         ->where(['node_id' => $child_id,'level' => $levelId, 'tpl_id' =>$tpl_id])
         ->asArray()
         ->one();
        return $btg_data ? $btg_data : null;
       
    }


    public static function getBtgDataUpdate($child_id, $tpl_id){
        $btg_data= AmazonBtg::find()
         ->where(['node_id' => $child_id, 'tpl_id' => $tpl_id])
         ->asArray()
         ->one();
        return $btg_data ? $btg_data : null;
       
    }
    


    /**
    * @
    * @return 
    */
    public static function getTplidByItemtype($item_type){
        $btg_data= AmazonBtg::find()
         ->where(['keyword' => $item_type])
         ->asArray()
         ->one();
        return $btg_data['tpl_id'] ? $btg_data['tpl_id'] : "";
    }

    public static function getCategoryByShopId($shopId, $parentId = 0)
    {
        $siteId = Store::findSiteidByShopid($shopId);
        if (!$siteId) {
            return [];
        }

        return self::getCategory($siteId, $parentId, null);
    }

    public static function insertData($siteData, $tplId){
        $AmazonBtgModel = new AmazonBtg();
        $AmazonBtgModel->node_id = $siteData['node_id'];
        $AmazonBtgModel->parent_id = $siteData['parent_id'];
        $AmazonBtgModel->top_id = $siteData['top_id'];
        $AmazonBtgModel->level = $siteData['level'];
        $AmazonBtgModel->leaf = $siteData['leaf'];
        $AmazonBtgModel->keyword = $siteData['keyword'];
        $AmazonBtgModel->node_name = $siteData['node_name'];
        $AmazonBtgModel->node_path = $siteData['node_path'];
        $AmazonBtgModel->tpl_id = $tplId;
        $AmazonBtgModel->site_id = $siteData['site_id'];
        return $AmazonBtgModel->save();
    }

    public static function deleteData($siteId, $tplId){
        AmazonBtg::deleteAll('site_id = :site_id AND tpl_id = :tpl_id', [':site_id' => $siteId, ':tpl_id' => $tplId]);
    }

}
