<?php 
namespace app\models;
use Yii;
use yii\web\IdentityInterface;
use yii\db\ActiveRecord;

use app\help;
use yii\data\Pagination;
use yii\db\Query;

/**
* [线上售卖商品 的主表] 的数据层
* add by echo 2016-06-13
*/
class GoodsSyncOnline extends ActiveRecord
{

    public static function tableName()
    {
        return 'sea_goods_sync_online';
    }

    public function selectByParam($param){
        $asin = isset($param['asins']) ? $param['asins'] : '';
        $shopId = isset($param['shopId']) ? $param['shopId'] : '';

        $query = GoodsSyncOnline::find();
        if(!empty($asin)){
            if(is_array($asin)){
                $query->andWhere(['in','asin',$asin]);
            }else{
                $query->andWhere(['asin' => $asin]);
            }
        }
        if(!empty($shopId)){
            if(is_array($shopId)){
                $query->andWhere(['in','shop_id',$shopId]);
            }else{
                $query->andWhere(['shop_id' => $shopId]);
            }
        }
        $goodsSyncOnlineData = $query->asArray()->all();
        return $goodsSyncOnlineData;
    }

    /**
     * @新建 线上售卖商品 信息存储
     * @param  array  $data
     * @return 线上售卖商品
     */
    public static  function createGoodsSyncOnline($data)
    {
        $goodsOnline = new GoodsSyncOnline();
        $goodsOnline->gmt_create = date("Y-m-d H:i:s");
        $goodsOnline->gmt_modified = $goodsOnline->gmt_create;
        $goodsOnline->creator = isset($data['userId']) ? $data['userId']:0;
        $goodsOnline->modifier = $goodsOnline->creator;

        $goodsOnline->goods_id = isset($data['goodsId']) ? $data['goodsId']:0;
        $goodsOnline->shop_id = isset($data['shopId']) ? $data['shopId']:0;

        $goodsOnline->asin = isset($data['asin']) ? $data['asin']:'';
        $goodsOnline->title = isset($data['title']) ? $data['title']:'';
        $goodsOnline->image_url = isset($data['image_url']) ? $data['image_url']:'';

        $goodsOnline->description = isset($data['description']) ? str_replace("\xE5\x93", "", $data['description']):'';
        $goodsOnline->item_type = isset($data['item_type']) ? $data['item_type']:'';
        $goodsOnline->shipping_fee = isset($data['shipping_fee']) ? $data['shipping_fee']:'0.00';
        $goodsOnline->image_url = isset($data['image_url']) ? $data['image_url']:'';

        $goodsOnline->last_sync_at = isset($data['lastSyncAt']) ? $data['lastSyncAt'] : 0;
        
        $goodsOnline->bullet_points = isset($data['bullet_points']) ? $data['bullet_points'] : '';

        $goodsOnline->keywords = isset($data['keywords']) ? $data['keywords'] : '';

        $goodsOnline->create_at = time();
        $goodsOnline->update_at = $goodsOnline->create_at;

        if($goodsOnline->save()){
            $id = $goodsOnline->attributes['id'];//数据保存后返回插入的ID
            $goodsOnline->id = $id;
            return $goodsOnline;
        }
        return null;
    }

    public function setFields(array $fields)
    {
        foreach ($fields as $key => $value) {
            $this->$key = $value;
        }
    }

    public function getSkus()
    {
        return $this->hasMany(GoodsSyncSku::className(), ['goods_online_id' => 'id'])
            ->onCondition([GoodsSyncSku::tableName() . '.is_deleted' => 'N']);
    }

    public function updateGoodsById($id, $shopId, array $params)
    {
        $self = self::findOne(['id' => $id, 'shop_id' => $shopId]);

        if (!$self) {
            return false;
        }

        $self->setFields($params);
        return $self->save();
    }

    public function getSyncGoods(array $params = array(), $shopId)
    {
        $pageSize = isset($params['page_no']) ? intval(trim($params['page_no'])) : 50;
        $goodsId = isset($params['goods_id']) ? intval(trim($params['goods_id'])) : 0;
        $title = isset($params['title']) ? trim($params['title']) : '';
        $asin = isset($params['asin']) ? trim($params['asin']) : '';
        $sku = isset($params['sku']) ? trim($params['sku']) : '';
        $isStock = isset($params['is_stock']) ? intval(trim($params['is_stock'])) : 0;

        $currName = $this->tableName();


        $query = self::find()->joinWith([
                'skus' => function ($q) use ($sku, $isStock) {
                    $skuName = GoodsSyncSku::tableName();
                    $q->select('id,goods_online_id,price,sale_price,current_price,stock,sales_rank,sku,shipping_fee,sales_begin_date,sales_end_date,is_adjustment_price,fulfillment_channel');
                    if ($sku) {
                        $q->andWhere(['=', "$skuName.sku", $sku]);
                    }
                    if ($isStock > 0) {
                        $q->andWhere(['>', "$skuName.stock", 0]);
                    }
                    if ($isStock < 0) {
                        $q->andWhere(["$skuName.stock" => 0]);
                    }
                }
            ])
            ->where(["$currName.is_deleted" => 'N', "$currName.shop_id" => $shopId])
            ->andWhere(['>', "$currName.last_sync_at", 0]);
        if ($title) {
            $query->andWhere(['like', "$currName.title", $title]);
        }
        if ($asin) {
            $query->andWhere(["$currName.asin" => $asin]);
        }
        if ($goodsId > 0) {
            $query->andWhere(["$currName.id" => $goodsId]);
            
            return $query->asArray()->all();
        }
// die($query->createCommand()->getRawSql());
        $pages = new Pagination([
            'defaultPageSize' => $pageSize,
            'totalCount' => $query->distinct('id')->count(),
            'pageSize' => $pageSize,
        ]);

        $query->orderBy("$currName.gmt_modified desc,$currName.id desc")
            ->offset($pages->offset)->limit($pages->limit);
        // die($query->createCommand()->getRawSql());
        $products = $query->asArray()->all();
        return compact('products', 'pages');

    }


    public function getSyncGoodsInfo($param){

        $query = new Query;
        $query  ->select(['*'])  
                ->from(GoodsSyncOnline::tableName())
                ->join( 'LEFT JOIN', 
                        GoodsSyncSku::tableName(),
                        GoodsSyncOnline::tableName().'.id ='.GoodsSyncSku::tableName().'.goods_online_id'
                    )
                ->where([GoodsSyncOnline::tableName() . '.id' => $param]);
        $command = $query->createCommand();
        $data = $command->queryAll();
        return $data[0];
    }

}

?>
