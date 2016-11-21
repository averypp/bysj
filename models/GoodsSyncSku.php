<?php 
namespace app\models;

use Yii;
use yii\web\IdentityInterface;
use yii\db\ActiveRecord;

use yii\helpers\ArrayHelper;
use Exception;

/**
* [线上售卖商品 的分表] 的数据层
* add by echo 2016-07
*/
class GoodsSyncSku extends ActiveRecord
{
	
	public static function tableName()
    {
        return 'sea_goods_sync_sku';

    }

    public function syncCurrentPrice($ids){
        if(empty($ids)){
            return null;
        }
        $sql = "update ".GoodsSyncSku::tableName()." set current_price=price where id in (".$ids.")";
        if (!Yii::$app->db->createCommand($sql)->execute()) {
            return false;
        }
        return true;
    }

    public function updateGoodsSyncSkuStock($param, $shopId){
        $sku = isset($param['seller-sku']) ? $param['seller-sku'] : '';
        if(empty($sku)){
            return false;
        }
        $goodsSyncSku = GoodsSyncSku::find()
            ->andWhere(["sku" => $sku, "shop_id" => $shopId])
            ->one();
        if(!$goodsSyncSku){
            return false;
        }

        $goodsSyncSku->price = $param['price'];
        $goodsSyncSku->current_price = $param['price'];
        $goodsSyncSku->sale_price = $param['price'];
        $goodsSyncSku->stock = $param['quantity'];
        $goodsSyncSku->asin = isset($param['asin1']) ? $param['asin1'] : '';
        $goodsSyncSku->shipping_fee = $param['shipping_fee'];
        $goodsSyncSku->fulfillment_channel = $param['fulfillment_channel'];
        $goodsSyncSku->gmt_modified = date("Y-m-d H:i:s");
        $goodsSyncSku->update_at = time();

        return $goodsSyncSku->update();
    }

    public function deleteGoodsSyncOnlineData($updateData, $shopId){
        if(empty($updateData)){
            return null;
        }
        $goodsSyncSku = GoodsSyncSku::find()
            ->andWhere(["shop_id" => $shopId,"sku" => $updateData['seller-sku']])
            ->one();
        if($goodsSyncSku){
            return $goodsSyncSku->delete();
        }
        return null;
    }

    public function selectByParam($param){
        $goodsOnlineIds = isset($param['goodsOnlineIds']) ? $param['goodsOnlineIds'] : '';
        $ids = isset($param['ids']) ? $param['ids'] : '';
        $skus = isset($param['skus']) ? $param['skus'] : '';
        $asins = isset($param['asins']) ? $param['asins'] : '';
        $shopId = isset($param['shopId']) ? $param['shopId'] : '';

        $query = GoodsSyncSku::find();
        if(!empty($goodsOnlineIds)){
            if(is_array($goodsOnlineIds)){
                $query->andWhere(['in','goods_online_id',$goodsOnlineIds]);
            }else{
                $query->andWhere(['goods_online_id' => $goodsOnlineIds]);
            }
        }
        if(!empty($skus)){
            if(is_array($skus)){
                $query->andWhere(['in','sku',$skus]);
            }else{
                $query->andWhere(['sku' => $skus]);
            }
        }
        if(!empty($asins)){
            if(is_array($asins)){
                $query->andWhere(['in','asin',$asins]);
            }else{
                $query->andWhere(['asin' => $asins]);
            }
        }
        if(!empty($ids)){
            if(is_array($ids)){
                $query->andWhere(['in','id',$ids]);
            }else{
                $query->andWhere(['id' => $id]);
            }
        }
        if(!empty($shopId)){
            if(is_array($shopId)){
                $query->andWhere(['in','shop_id',$shopId]);
            }else{
                $query->andWhere(['shop_id' => $shopId]);
            }
        }
        $goodsSyncSkuData = $query->asArray()->all();
        return $goodsSyncSkuData;
    }

    /**
     * @新建 线上售卖商品分录 信息存储
     * @param  array  $data
     * @return 线上售卖商品分录
     */
    public static  function createGoodsSyncSku($data)
    {
        $goodsOnlineSku = new GoodsSyncSku();
        $goodsOnlineSku->gmt_create = date("Y-m-d H:i:s");
        $goodsOnlineSku->gmt_modified = $goodsOnlineSku->gmt_create;
        $goodsOnlineSku->creator = isset($data['userId']) ? $data['userId']:0;
        $goodsOnlineSku->modifier = $goodsOnlineSku->creator;

        $goodsOnlineSku->goods_online_id = isset($data['goodsOnlineId']) ? $data['goodsOnlineId'] : 0;//关联字段 一定要有
        $goodsOnlineSku->sku = $data['sku'];
        $goodsOnlineSku->price = $data['price'];
        $goodsOnlineSku->current_price = $data['current_price'];
        $goodsOnlineSku->sale_price = $data['salePrice'];
        $goodsOnlineSku->stock = $data['stock'];
        $goodsOnlineSku->sales_rank = isset($data['salesRank']) ? $data['salesRank'] : 0;
        $goodsOnlineSku->asin = isset($data['asin']) ? $data['asin'] : '';
        $goodsOnlineSku->shop_id = $data['shop_id'];
        $goodsOnlineSku->shipping_fee = $data['shipping_fee'];
        $goodsOnlineSku->fulfillment_channel = $data['fulfillment_channel'];
        $goodsOnlineSku->create_at = time();
        $goodsOnlineSku->update_at = $goodsOnlineSku->create_at;

        if($goodsOnlineSku->save()){
            $id = $goodsOnlineSku->attributes['id'];//数据保存后返回插入的ID
            $goodsOnlineSku->id = $id;
            return $goodsOnlineSku;
        }
        return null;
    }

    public static function modifyPriceStock($goodsId, array $sliceSql, array $condition = array())
    {

        if (!$goodsId || !$sliceSql) {
            return false;
        }

        $_now = time();
        $gmt = date('Y-m-d H:i:s', $_now);
        settype($goodsId, 'array');

        $where = ['goods_online_id' => $goodsId, 'is_deleted' => 'N'];
        if ($condition) {
            $where = array_merge($where, $condition);
        }

        $skuIds = GoodsSyncSku::find()->select(['id'])->where($where)->asArray()->all();
        $skuIds = ArrayHelper::getColumn($skuIds, 'id');

        if (!$skuIds) {
            return false;
        }

        // 价格或库存未修改则不更新
        $query = GoodsSyncSku::find()->where(['id' => $skuIds]);
        foreach ($sliceSql as $k => $v) {
                $query->andWhere([$k => "$v"]);
        }
        $tmpIds = $query->select(['id'])->asArray()->all();
        $tmpIds = ArrayHelper::getColumn($tmpIds, 'id');

        $validIds = array_diff($skuIds, $tmpIds);
        // die($query->createCommand()->getRawSql());
        if (!$validIds) {
            return false;
        }

        $sql = "update sea_goods_sync_sku set ";
        $where = ' where id in (' . implode(',', $validIds) . ')';

        $upSql = [];
        $sliceSql = array_merge($sliceSql, ['update_at' => $_now, 'gmt_modified' => $gmt]);
        foreach ($sliceSql as $key => $value) {
            if (strpos($value, $key) === false) {
                $upSql[] = "`$key` = '$value'";
            } else {
                $upSql[] = "`$key` = $value";
            }
        }

        $sql = $sql . implode(',', $upSql) . $where;
        // die($sql);
        if (!Yii::$app->db->createCommand($sql)->execute()) {
            return false;
        }

        return $validIds;
    }



    public function getSyncGoodsInfo($param){
        $query = GoodsSyncSku::find();
        if(!empty($param)){
            if(is_array($param)){
                $query->andWhere(['in','goods_online_id',$param]);
            }else{
                $query->andWhere(['goods_online_id' => $param]);
            }
        }
        $goodsSyncOnlineData = $query->asArray()->all();
        // $query->createCommand()->getRawSql()''
        return $goodsSyncOnlineData;
    }
    public function findInfoById($id){
        $query = GoodsSyncSku::find();
        $query->select(['id', 'sku', 'goods_online_id', 'shop_id', 'sale_price', 'shipping_fee']);
        if($id){
            $query->andWhere(['id' => $id]);
        }
        $goodsSyncSkuData = $query->asArray()->one();
        // $query->createCommand()->getRawSql()''
        return $goodsSyncSkuData;
    }

}

?>