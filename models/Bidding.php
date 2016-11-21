<?php

namespace app\models;

use Yii;
use app\models\GoodsSyncSku;

class Bidding extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sea_bidding';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'sku_id', 'my_price', 'create_at'], 'required'],
            [['goods_id', 'sku_id', 'status', 'rules_id', 'competitors_count', 'create_at', 'update_at', 'last_modifyprice_at'], 'integer'],
            [['cost', 'mix_price', 'max_price', 'my_price', 'lower_price', 'buybox_price'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'goods_id' => 'Goods ID',
            'sku_id' => 'Sku ID',
            'status' => 'Status',
            'cost' => 'Cost',
            'mix_price' => 'Mix Price',
            'max_price' => 'Max Price',
            'rules_id' => 'Rules ID',
            'competitors_count' => 'Competitors Count',
            'my_price' => 'My Price',
            'lower_price' => 'Lower Price',
            'buybox_price' => 'Buybox Price',
            'create_at' => 'Create At',
            'update_at' => 'Update At',
            'last_modifyprice_at' => 'Last Modifyprice At',
        ];
    }

    public function getRules()
    {
        return $this->hasOne(BiddingRules::className(), ['id' => 'rules_id']);
    }

    public function getSku()
    {
        return $this->hasOne(GoodsSyncSku::className(), ['id' => 'sku_id']);
    }

    public function getGoods()
    {
        return $this->hasOne(GoodsSyncOnline::className(), ['id' => 'goods_id']);
    }

    public function getBiddingGoods(array $params = array(), $shopId){
        $pageSize = isset($params['page_no']) ? intval(trim($params['page_no'])) : 10;
        $title = isset($params['title']) ? trim($params['title']) : '';
        $asin = isset($params['asin']) ? trim($params['asin']) : '';
        $sku = isset($params['sku']) ? trim($params['sku']) : '';
        $filter = isset($params['filter']) ? intval(trim($params['filter'])) : 0;

    }

    public static function addBidding($sku){
        $now = time();
        $BiddingModel = new Bidding();
        $BiddingModel->goods_id = $sku['goods_online_id'];
        $BiddingModel->sku_id = $sku['id'];
        $BiddingModel->status = 0;
        $BiddingModel->shop_id = $sku['shop_id'];
        if (strtotime($sku['sales_begin_date']) < $now && strtotime($sku['sales_end_date']) > $now) {
            $BiddingModel->my_price = $sku['sale_price'];
        } else {
            $BiddingModel->my_price = $sku['price'];
        }
        $BiddingModel->my_price_fare = $sku['shipping_fee'];
        $BiddingModel->create_at = $now;
        return $BiddingModel->save();
    }
    public static function editBidding($id, $APIData){
        $BiddingModel = Bidding::find()
            ->andWhere(["id" => $id])
            ->one();
        if(isset($APIData['cost'])){
            $BiddingModel->cost = $APIData['cost'];
        }
        if(isset($APIData['mix_price'])){
            $BiddingModel->mix_price = $APIData['mix_price'];
        }
        if(isset($APIData['max_price'])){
            $BiddingModel->max_price = $APIData['max_price'];
        }
        if(isset($APIData['rules_id'])){
            $BiddingModel->rules_id = $APIData['rules_id'];
        }
        if(isset($APIData['competitors_count'])){
            $BiddingModel->competitors_count = $APIData['competitors_count'];
        }
        if(isset($APIData['my_price'])){
            $BiddingModel->my_price = $APIData['my_price'];
        }
        if(isset($APIData['lower_price'])){
            $BiddingModel->lower_price = $APIData['lower_price'];
        }
        if(isset($APIData['buybox_price'])){
            $BiddingModel->buybox_price = $APIData['buybox_price'];
        }
        $BiddingModel->update_at = time();
        if($BiddingModel->save()){
            return true;
        }
        return false;
    }

    public static function batchEditeBidding($ids, $status){
        $updateStatus = ['status' => $status];
        $where = ['id' => $ids];
        if (!Bidding::updateAll($updateStatus, $where)) {
            return false;
        }
        return true;
    }
    public static function batchCleanRule($ids){
        $updateStatus = ['rules_id' => 0];
        $where = ['id' => $ids];
        if (!Bidding::updateAll($updateStatus, $where)) {
            return false;
        }
        return true;
    }
    public static function batchRemoveGoods($ids, $skuIds){
        $where = ['id' => $ids];
        $transaction = Yii::$app->db->beginTransaction();
        try {
            Bidding::deleteAll($where);
            GoodsSyncSku::updateAll(['is_adjustment_price' => 0], ['id' => $skuIds]);
            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }
    public static function getBidInfo($shopId){
        return Bidding::find()->select(['rules_id', 'id'])->where("shop_id =".$shopId)->asArray()->all();
    }
    
}
