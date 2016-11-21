<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class Store extends ActiveRecord 
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sea_store';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['store_name', 'user_id', 'platform_id', 'site_id', 'merchant_id', 'accesskey_id', 'secret_key'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'store_name' => 'store_name',
            'user_id' => 'user_id',
            'platform_id'   => 'platform_id',
            'site_id' => 'site_id',
        ];
    }
    
    public static function saveStore(array $data)
    {   

        $id = 0;
        $_now = time();

        if (isset($data['id']) && $data['id']) {
            $id = abs(intval($data['id']));
            $model = Store::findOne($id);
            if (!$model) {
                return false;
            }
        } else {
            $model = new Store();
        }

        $model->gmt_create = date("Y-m-d H:i:s", $_now);
        $model->gmt_modified = $model->gmt_create;
        $model->creator = isset($data['user_id']) ? intval($data['user_id']) : null;
        $model->modifier = isset($data['user_id']) ? intval($data['user_id']) : null;
        $model->store_name = isset($data['store_name']) ? $data['store_name'] : null;
        $model->site_id = isset($data['site_id']) ? $data['site_id'] : null;
        $model->user_id = isset($data['user_id']) ? intval($data['user_id']) : null;
        $model->platform_id = isset($data['platform_id']) ? $data['platform_id'] : null;
        $model->merchant_id = isset($data['merchant_id']) ? $data['merchant_id'] : null;
        $model->accesskey_id = isset($data['accesskey_id']) ? $data['accesskey_id'] : null;
        $model->secret_key = isset($data['secret_key']) ? $data['secret_key'] : null;

        $countQuery = Store::find()
            ->where(['site_id' => $model->site_id, 'user_id' => $model->user_id])
            ->andWhere(['platform_id' => $model->platform_id])
            ->andWhere(['store_name' => $model->store_name]);
        if ($id > 0) {
            $count = $countQuery->andWhere(['<>', 'id', $id])->count();
        } else {
            $count = $countQuery->count();
        }

        if ($count > 0) {
            return false;
        }

        if (!$model->save()) {
            return false;
        }

        return $model->id;
    }

    public function getPlatform()
    {
        return $this->hasOne(Platform::className(), ['id' => 'platform_id']);
    }

    public function getSite()
    {
        return $this->hasOne(Platform::className(), ['id' => 'site_id']);
    }

    public function getStores($userId, $hasGoodsCount = true)
    {

        $userId = abs(intval($userId));

        $stores = Store::find()->with('platform')->with('site')
            ->where(['user_id' => $userId, 'is_deleted' => 'N'])->asArray()->all();

        if (!$hasGoodsCount) {
            return $stores;
        }

        $goodsCount = [];
        if ($stores) {

            $store_ids = ArrayHelper::getColumn($stores, 'id');

            $goodsCount = GoodsInfo::find()
                ->select('shop_id, count(1) as total')
                ->where(['in', 'shop_id', $store_ids])
                ->groupBy('shop_id')
                ->asArray()
                ->all();

            $goodsCount = ArrayHelper::index($goodsCount, 'shop_id');
            
        }

        foreach ($stores as $key => &$store) {
            $store['goodsCount'] = 0;
            if (isset($goodsCount[$store['id']])) {
                $store['goodsCount'] = $goodsCount[$store['id']]['total'];
            }
        }

        return $stores;
    }

    /**
     *
     * @param  id
     * @return static|null
     */
    public static function findById($id,$userId)
    {
        if(empty($id) || empty($userId)){
            return null;
        }
          $store = Store::find()
            ->where(['id' => $id,'user_id' =>$userId,'is_deleted'=>"N" ])
            ->asArray()
            ->one();
            if($store){
                return new static($store);
            }

        return null;
    }
    /**
     *
     * @param  shopId
     * @return static|null
     */
    public static function getSiteIdByShopId($shopId)
    {
        if(empty($shopId)){
            return null;
        }
          $store = Store::find()
            ->where(['id' => $shopId,'is_deleted'=>"N" ])
            ->asArray()
            ->one();
            if($store){
                return $store['site_id'];
            }
        return null;
    }

    /**
     *
     * @param  shopId
     * @return static|null
     */
    public static function getInfoById($storeId)
    {
        if(empty($storeId)){
            return null;
        }
          $store = Store::find()
            ->where(['id' => $storeId,'is_deleted'=>"N" ])
            ->asArray()
            ->one();
            if($store){
                return $store;
            }

        return null;
    }


    public static function editStoreName($id,$userId,$storeName){
        if(empty($id) || empty($userId) || empty($storeName)){
            return null;
        }
        $store = Store::find()
            ->where(['id' => $id,'user_id' =>$userId,'is_deleted'=>"N" ])
            ->one();
        if(!$store){
            return false;
        }
        $store->store_name = $storeName;
        if($store->update()){
            return true;
        }
        return false;
    }

    public static function delStore($id,$userId){
        if(empty($id) || empty($userId) ){
            return null;
        }
        $store = Store::find()
            ->where(['id' => $id,'user_id' =>$userId,'is_deleted'=>"N" ])
            ->one();
        if(!$store){
            return false;
        }
        $store->is_deleted = "Y";
        if($store->update()){
            return true;
        }
        return false;

    }

    public static function storeHasExists($storeId, $userId)
    {
        if (!$storeId || !$userId) {
            return false;
        }

        $query = Store::find()->where(['id' => $storeId, 'user_id' => $userId, 'is_deleted' => 'N']);

        return $query->one() ? true : false;
    }

    public static function getStoreInfo($shopId)
    {
        if ($shopId <= 0) {
            return false;
        }

        return Store::findOne($shopId);

    }

    public static function findSiteidByShopid($storeId)
    {
        if ($storeId <= 0) {
            return false;
        }
        $store_info = Store::find()->where(['id' => $storeId, 'is_deleted' => 'N'])->one();
        if (!$store_info) {
            return false;
        }

        return $store_info->site_id;

    }

    //不带任何参数：用于定时任务
    public static function findStoreInfo(){

        $stores = Store::find()
            ->with('platform')
            ->with('site')
            ->where(['is_deleted' => "N"])
            ->asArray()
            ->all();
        return $stores;
    }

}
