<?php
namespace app\models;
use Yii;
use yii\db\ActiveRecord;
use app\models\AmazonFeedTplData;
use app\models\GoodsSoldInfo;
use app\models\GoodsParams;
use app\models\GoodsPicture;
use app\models\GoodsSpec;
use app\models\AmazonFeeds;
use app\models\AmazonBtg;
use Exception;

class GoodsInfo extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sea_goods_entity';  // '{{user}}';

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
            //'id' => 'ID',
            //'username' => 'Username',
           // 'password' => 'password',
        	//'mobile'   => 'mobile',
           // 'teg_time' => 'teg_time',
        ];
    }
    function recordGoods($product_data, $shopId, $pub_status = 0, $goodsId = null, $update = false){
        //var_dump($product_data);
        $transaction = Yii::$app->db->beginTransaction();
        // insert goods_entity
        $goodsId = $this->insertEntityTable($product_data, $shopId, $pub_status, $goodsId);
        if(!$goodsId){
            $transaction->rollBack();
        }
        if($update){
            GoodsSoldInfo::deleteAll("goods_id = ".$goodsId);
            GoodsPicture::deleteAll("goods_id = ".$goodsId);
            GoodsSpec::deleteAll("goods_id = ".$goodsId);
            GoodsParams::deleteAll("goods_id = ".$goodsId);
        }
        $res = $this->insertSkuTable($goodsId, $shopId, $product_data);
        if (!$res) {
            $transaction->rollBack();
        }
        if($product_data['ProductSpecifics']){
            if (!$this->insertParamsTable($goodsId, $product_data['ProductSpecifics'])) {
                $transaction->rollBack();
            }
        }
        $transaction->commit();
        return $goodsId;
    }


    function insertEntityTable ($product_data, $shopId, $pub_status = 0 ,$goodsId = null){
        if($goodsId){
            $goodsInfo_model = GoodsInfo::findOne(['id' => $goodsId]);
        } else {
            $goodsInfo_model = new GoodsInfo();
            $goodsInfo_model->gmt_create = date('Y-m-d H:i:s');
        }
        //var_dump($product_data);die;
        $goodsInfo_model->gmt_modified = date('Y-m-d H:i:s');
        $goodsInfo_model->shop_id = $shopId;
        $goodsInfo_model->supply_link = $product_data['SupplyLink'];
        $goodsInfo_model->item_name = $product_data['Title'] ? : '';
        $goodsInfo_model->item_type = $product_data['ItemType'] ? : '';//table btg keyword
        $goodsInfo_model->feed_product_type = $product_data['ProductType'];
        $goodsInfo_model->is_brand = $product_data['BrandSeller'] == 1 ? 'Y' : 'N';
        $goodsInfo_model->product_description = $product_data["Description"] ? : '  '; //描述
        $goodsInfo_model->stocking_time = $product_data['DispatchTimeMax'] ? : 0;//备货时间
        $goodsInfo_model->brand_name = $product_data['Brand'] ? : '';
        $goodsInfo_model->manufacturer = $product_data['Manufacture'] ? : '' ;
        $goodsInfo_model->condition_type = $product_data["Condition"]["Name"] ? : 'New';
        $goodsInfo_model->list_price = $product_data['MSRP'];            //制造商建议零售价
        $goodsInfo_model->external_product_id_type = $product_data['ProductIdType'] ? : '';//商品编码类型
        $goodsInfo_model->website_shipping_weight =$product_data['ShippingWeight'] ? : '';   //邮寄重量
        $goodsInfo_model->website_shipping_weight_unit_of_measure = $product_data['WeightUnit']? : '';
        $goodsInfo_model->generic_keywords = implode(',',$product_data['KeyWords']);
        $goodsInfo_model->bullet_points = serialize($product_data['BulletPoints']);
        $goodsInfo_model->pub_status = $pub_status;
        $goodsInfo_model->parent_sku = $product_data['ParentSKU'];
        $goodsInfo_model->variation_theme = $product_data['VariationTheme'];
        $goodsInfo_model->category_id = $product_data['Category']['ID'];
        $goodsInfo_model->tpl_id = $product_data['tpl_id'] ? : 0;
        $categoryName = '';
        foreach ($product_data['Category']['Name'] as $key => $value) {
            $categoryName .= $key == 0 ? $value : '>'.$value;
        }
        if(strpos($categoryName, '未设置分类')){
            $categoryName = ' ';
        }
        $goodsInfo_model->category_name = $categoryName;
        $goodsInfo_model->save();
        if(!$goodsId){
            $goodsId = $goodsInfo_model->primaryKey;
        }
        return $goodsId;

    }

    public  function insertSkuTable($goodsId, $shopId, $product_data){
        //$datas = [];
        $transaction = Yii::$app->db->beginTransaction();
        if($product_data['VariationTheme'] == ''){
            $goodsSoldInfo_model = new GoodsSoldInfo();
            $goodsSoldInfo_model->gmt_create = date('Y-m-d H:i:s');
            $goodsSoldInfo_model->gmt_modified = date('Y-m-d H:i:s');
            $goodsSoldInfo_model->shop_id = $shopId;
            $goodsSoldInfo_model->goods_id = $goodsId;
            $goodsSoldInfo_model->external_product_id = $product_data["UPC"] ? : ""; //商品编码
            $goodsSoldInfo_model->standard_price = $product_data['StartPrice'] ? : '';
            $goodsSoldInfo_model->sale_price = $product_data['Sale']['SalePrice'] ? : '';
            $goodsSoldInfo_model->sale_from_date = $product_data['Sale']['SaleDateFrom'] ? : '';
            $goodsSoldInfo_model->sale_end_date = $product_data['Sale']['SaleDateTo'] ? : '';
            $goodsSoldInfo_model->quantity = $product_data['Quantity'] ? : 0;
            $goodsSoldInfo_model->item_sku = $product_data['ParentSKU'] ? : '';
            
            if ($goodsSoldInfo_model->save()) {
                $skuId = $goodsSoldInfo_model->primaryKey;
                if($product_data['PictureURLs']){
                    $res = $this->insertPicture($goodsId, $shopId, $skuId, $product_data['PictureURLs'], $is_child = 0);
                    if (!$res) {
                        $transaction->rollBack();
                        return false;
                    }
                }
            } else {
                $transaction->rollBack();
                return false;
            }
            
        } else {
            //父图片插入次数
            $pic_insert_time = 1;
            foreach ($product_data['ProductSKUs'] as $key => $value) {
                $goodsSoldInfo_model = new GoodsSoldInfo();
                $goodsSoldInfo_model->gmt_create = date('Y-m-d H:i:s');
                $goodsSoldInfo_model->gmt_modified = date('Y-m-d H:i:s');
                $goodsSoldInfo_model->shop_id = $shopId;
                $goodsSoldInfo_model->goods_id = $goodsId;
                $goodsSoldInfo_model->external_product_id = $value["UPC"] ? : ""; //商品编码
                $goodsSoldInfo_model->standard_price = $value['Price'] ? : '';
                $goodsSoldInfo_model->sale_price = $value['Sale']['SalePrice'] ? : '';
                $goodsSoldInfo_model->sale_from_date = $value['Sale']['SaleDateFrom'] ? : '';
                $goodsSoldInfo_model->sale_end_date = $value['Sale']['SaleDateTo'] ? : '';
                $goodsSoldInfo_model->quantity = $value['Stock'] ? : 0;
                $goodsSoldInfo_model->item_sku = $value['SKU'] ? : '';
                $goodsSoldInfo_model->parent_child = 'child';
                $goodsSoldInfo_model->relationship_type = 'Variation';

                if ($goodsSoldInfo_model->save()) {
                    $skuId = $goodsSoldInfo_model->primaryKey;

                    if(!$this->insertSpecTable($goodsId, $skuId, $value['VariationSpecifics'])){
                        $transaction->rollBack();
                        return false;
                    }
                    if($value['PictureURL']){
                        if(!$this->insertPicture($goodsId, $shopId, $skuId, $value['PictureURL'], $is_child = 0)){
                            $transaction->rollBack();
                            return false;
                        }
                    }
                    if($product_data['PictureURLs'] && $pic_insert_time == 1){
                        if (!$this->insertPicture($goodsId, $shopId, -1, $product_data['PictureURLs'], $is_child = 1)){
                            $transaction->rollBack();
                            return false;
                        } else {
                            $pic_insert_time++;
                        }
                    }
                } else {
                    $transaction->rollBack();
                    return false;
                }
            }
        }
        $transaction->commit();
        return true;
    }
    function insertPicture($goodsId, $shopId, $skuId, $ImageArray, $is_child = 0){
        $datas = [];
        if(!$ImageArray || !is_array($ImageArray)){
            return false;
        }
        foreach ($ImageArray as $key => $value) {
            $one = [
                'gmt_create' => date('Y-m-d H:i:s'),
                'gmt_modified' => date('Y-m-d H:i:s'),
                'shop_id' => $shopId,
                'sku_id' => $skuId,
                'goods_id' => $goodsId,
                'image_url' => $value,
                'goods_picture_type' => $is_child
            ];
            $datas[] = $one;
        }
        return Yii::$app->db->createCommand()->batchInsert(GoodsPicture::tableName(), array_keys($datas[0]), array_values($datas))->execute();
    }
    function insertParamsTable($goodsId, $productSpecifics){
        if( !$productSpecifics || !is_array($productSpecifics) ){
            return false;
        }
        $datas = [];
        foreach ($productSpecifics as $key => $value) {
            $one = [
                'gmt_create' => date('Y-m-d H:i:s'),
                'gmt_modified' => date('Y-m-d H:i:s'),
                'goods_id' => $goodsId,
                'field' => $value['Name'] ? : '',
                'value' => $value['Value'] ? : '',
            ];
            $datas[] = $one;
        }
        return Yii::$app->db->createCommand()->batchInsert(GoodsParams::tableName(), array_keys($datas[0]), array_values($datas))->execute();
    }
    function insertSpecTable($goodsId, $skuId, $variationSpecifics){
        if( !$variationSpecifics || !is_array($variationSpecifics) ){
            return false;
        }
        $datas = [];
        foreach ($variationSpecifics as $key => $value) {
            $one = [
                'gmt_create' => date('Y-m-d H:i:s'),
                'gmt_modified' => date('Y-m-d H:i:s'),
                'goods_id' => $goodsId,
                'sku_id' => $skuId,
                'field' => $value['Name'] ? : '',
                'value' => $value['Value'] ? : '',
            ];
            $datas[] = $one;
        }
        return Yii::$app->db->createCommand()->batchInsert(GoodsSpec::tableName(), array_keys($datas[0]), array_values($datas))->execute();
    }

    //根据商品id获取商品所有信息，关联三张表
    public static function getProductInfoByIdForTpl($goodId){
         $goodsData= GoodsInfo::find()
         ->with('skus', 'params')
         ->where(['id' => $goodId])
         ->asArray()
         ->one();
        return $goodsData;
    }

    public function getSkus()
    {
        return $this->hasMany(GoodsSoldInfo::className(), ['goods_id' => 'id'])->with('specs', 'pic');
    }

    public function getParams()
    {
        return $this->hasMany(GoodsParams::className(), ['goods_id' => 'id']);
    }

    //修改,取各个表中数据
    public static function getProductInfoById($goodId, $shopId){
       $goodsData= GoodsInfo::find()
         ->with('skus', 'params')
         ->where(['id' => $goodId])
         ->asArray()
         ->one();
        if($goodsData['shop_id'] != $shopId){
            die('good not belongs to this shop');
        }
        return $goodsData;
    }
    public static function getParentImgById($goodId, $shopId){
       $imgData= GoodsPicture::find()
         ->select('image_url')
         ->where(['goods_id' => $goodId, 'shop_id' => $shopId, 'sku_id' => -1, 'goods_picture_type' =>1])
         ->asArray()->all();
        return $imgData;
    }
    
    public static function getTplById($tpl_id){
    	 $sql="select field from sea_amazon_feed_tpl_data where tpl_id =".$tpl_id;
    	 $command =  Yii::$app->db->createCommand($sql);
    	 $datas = $command->queryColumn();
    	 return $datas;
    }


}
