<?php 

namespace app\models;

use Yii;
use Exception;
use yii\web\IdentityInterface;
use yii\db\ActiveRecord;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use app\libraries\MyHelper;

/**
* 产品的数据层
* add by echo 2016-06-02
*/
class Product extends ActiveRecord 
{
	

	public static function tableName()
    {
        return 'sea_goods_entity';

    }

    public function getMainImages()
    {
        return $this->hasMany(GoodsPicture::className(), ['goods_id' => 'id']);
    }

    public function getSkus()
    {
        return $this->hasMany(GoodsSoldInfo::className(), ['goods_id' => 'id']);
    }

    public function getRequiredFields()
    {
        return $this->hasMany(AmazonFeedTplData::className(), ['tpl_id' => 'tpl_id'])
            ->viaTable('sea_amazon_btg', ['node_id' => 'category_id']);
    }

    public function getShopGoodsList($shopId, array $params = array())
    {

        $pageSize = isset($params['page_no']) ? intval(trim($params['page_no'])) : 30;

        $productId = isset($params['product_id']) ? intval(trim($params['product_id'])) : null;
        // var_dump($productId);die;
        $goodsName = isset($params['goods_name']) ? trim($params['goods_name']) : '';
        $pubStatus = isset($params['status']) ? intval(trim($params['status'])) : null;
        $itemSku = isset($params['sku']) ? trim($params['sku']) : '';

        $query = Product::find()
            ->select(['sea_goods_entity.*', 'sea_amazon_feeds.results'])
            ->leftJoin('sea_amazon_feeds', 'sea_amazon_feeds.good_id = sea_goods_entity.id')
            ->with([
                'mainImages' => function ($qImage) {
                    $qImage->select(['image_url', 'goods_id']);
                }
            ])
            ->with([
                'skus' => function ($qSku) {
                    $qSku->select(['goods_id', 'standard_price']);
                }
            ])
            ->where(['sea_goods_entity.is_deleted' => 'N', 'sea_goods_entity.shop_id' => $shopId]);

        if ($pubStatus !== null) {
            $query->andWhere(['sea_goods_entity.pub_status' => $pubStatus]);
        }
        if ($productId !== null) {
            $query->andWhere(['sea_goods_entity.id' => $productId]);
        }
        if ($goodsName) {
            $query->andWhere(['like', 'sea_goods_entity.item_name', $goodsName]);
        }
        if ($itemSku) {
            $existsSql = "EXISTS(SELECT 1 FROM sea_goods_sold_info WHERE sea_goods_sold_info.goods_info_id = sea_goods_entity.id AND sea_goods_sold_info.item_sku = '{$itemSku}')";
            $query->andWhere($existsSql);
        }
        // die($query->createCommand()->getRawSql());

        $pages = new Pagination([
            'defaultPageSize' => $pageSize,
            'totalCount' => $query->count(),
            'pageSize' => $pageSize,
        ]);

        $products = $query->orderBy('id')->offset($pages->offset)
            ->orderBy('sea_goods_entity.gmt_modified desc,sea_goods_entity.id desc')->limit($pages->limit)->asArray()->all();
        foreach ($products as &$val) {
            if ($val['mainImages']) {
                $val['mainImages'] = $val['mainImages'][0]['image_url'];
            } else {
                $val['mainImages'] = '/image/no-image.gif';
            }
            $val['statusMsg'] = '';
            if ($val['pub_status'] == 4) {
                $val['statusMsg'] = '上传成功';
            } elseif ($val['pub_status'] == 2) {
                $val['statusMsg'] = $val['dealing_status'] == 'upload' ? '<span style="color: #f0ad4e;">上传中</span>' : '编译中';
            }

            if (isset($val['check_error_msg']) && !empty($val['check_error_msg'])) {
                $val['check_error_msg'] = unserialize($val['check_error_msg']);
            } else {
                $val['check_error_msg'] = [];
            }

            $price = array_column($val['skus'], 'standard_price');
            $val['price'] = count($price) > 1 ? min($price) . '-' . max($price) : reset($price);
        }

        return compact('products', 'pages');

    }

    public function getStatusCount($shopId)
    {

        $select = [
            "SUM(CASE WHEN pub_status = 0 THEN 1 ELSE 0 END) AS draft",
            "SUM(CASE WHEN pub_status = 1 THEN 1 ELSE 0 END) AS waiting",
            "SUM(CASE WHEN pub_status = 2 THEN 1 ELSE 0 END) AS dealing",
            "SUM(CASE WHEN pub_status = 3 THEN 1 ELSE 0 END) AS failed",
            "SUM(CASE WHEN pub_status = 4 THEN 1 ELSE 0 END) AS success",
        ];

        $totalCount = Product::find()->select($select)->where(['shop_id' => $shopId, 'is_deleted' => 'N'])->asArray()->one();

        return $totalCount;
    }

    public function softDeletePorduct(array $productIds, $shopId, $pubStatus = '')
    {

        if (!$productIds) {
            return false;
        }

        list($validProductIds, $errorProductIds) = $this->_checkProductIds($productIds, [['shop_id' => $shopId]]);
        if (!$validProductIds) {
            return false;
        }

        $returnMsg = 
        $_now = date("Y-m-d H:i:s");
        $transaction = Yii::$app->db->beginTransaction();
        try {
            
            $deleteData = ['is_deleted' => 'Y', 'gmt_modified' => $_now];
            $where = ['id' => $validProductIds, 'is_deleted' => 'N'];
            if ($pubStatus) {
                $where['pub_status'] = $pubStatus;
            }

            if (!Product::updateAll($deleteData, $where)) {
                throw new Exception("Error", 1);
            }

            GoodsSoldInfo::updateAll($deleteData, ['goods_id' => $validProductIds]);
            GoodsParams::updateAll($deleteData, ['goods_id' => $validProductIds]);
            GoodsSpec::updateAll($deleteData, ['goods_id' => $validProductIds]);
            AmazonFeeds::updateAll($deleteData, ['good_id' => $validProductIds]);
            GoodsPicture::updateAll($deleteData, ['goods_id' => $validProductIds]);
            $transaction->commit();
            return ['failIds' => $errorProductIds, 'succIds' => $validProductIds];
        } catch (Exception $e) {
            var_dump($e->getMessage());
            $transaction->rollBack();
            return false;
        }

    }

    public function changeDealingStatus(array $ids, $dealingStatus, $shopId)
    {

        if (!$ids) {
            return false;
        }

        if ($dealingStatus == 'upload') {
            $condition = [['IS', 'check_error_msg', NULL]];
        } else {
            $condition = [];
        }
        list($validIds, $errorIds) = $this->_checkProductIds($ids, $condition);
        if (!$validIds) {
            return false;
        }

        $errMsgs = $this->checkItems($validIds);
        if ($errMsgs) {
            $noCheckIds = array_keys($errMsgs);
            $validIds = array_diff($validIds, $noCheckIds);
            $errorIds = array_unique(array_merge($errorIds, $noCheckIds));
            if (!$validIds) {
                return ['failIds' => $errorIds, 'succIds' => $validIds];
            }
        }

        $_now = time();
        $gmt = date('Y-m-d H:i:s', $_now);
        $where = ['is_deleted' => 'N', 'id' => $validIds];
        $data = [
            'dealing_status' => $dealingStatus,
            'pub_status' => 2,
            'gmt_modified' => $gmt,
        ];

        // redis做为队列服务
        foreach ($validIds as $id) {
            $args = [
                'publishGoods',
                ['goods_id' => $id, 'shop_id' => $shopId],
            ];
            \app\libraries\Queue::enqueue('PublishGoods', $args, 'publishGoods_one');
        }
        Product::updateAll($data, $where);

        return ['failIds' => $errorIds, 'succIds' => $validIds];

        // 旧的处理方式（数据库做为队列使用）
        /*$transaction = Yii::$app->db->beginTransaction();
        try {
            if (!Product::updateAll($data, $where)) {
                throw new Exception("update error.", 1);
            }
            $insertDatas = [];
            $fields = ['goods_id', 'shop_id', 'create_at'];
            foreach ($validIds as $id) {
                $one = [$id, $shopId, $_now];
                $insertDatas[] = $one;
            }
            $ret = Yii::$app->db->createCommand()->batchInsert(QueuePostgoods::tableName(), $fields, $insertDatas)->execute();
            if (!$ret) {
                throw new Exception("insert error.", 1);
            }
            $transaction->commit();

            return ['failIds' => $errorIds, 'succIds' => $validIds];
        } catch (Exception $e) {
            
            $transaction->rollBack();
            return false;
        }*/

    }

    public function checkItems($itemIds)
    {

        $gmt = date('Y-m-d H:i:s');

        $fieldsData = AmazonFeeds::find()->select(['good_id', 'data', 'tpl_id'])
            ->with([
                'tplData' => function ($q) {
                    $q->select(['tpl_id', 'field'])->where(['required' => 'Required']);
                }
            ])
            ->where(['good_id' => $itemIds])->andWhere(['IS', 'status', NULL])->asArray()->all();

        $errors = [];
        $fieldMessage = AmazonFeeds::fieldMessage();
        foreach ($fieldsData as $one) {
            $error = [];
            $requiredFields = array_column($one['tplData'], 'field');

            if (!$one['data']) {

                foreach ($requiredFields as $field) {
                    if (isset($fieldMessage[$field])) {
                        $error[] = $fieldMessage[$field];
                    }
                }
            } else {

                $requiredFields = array_flip($requiredFields);
                $data = explode("\n", $one['data']);
                array_shift($data);
                array_shift($data);
                $data = MyHelper::text2Array(implode("\n", $data));
                if (count($data) > 1) {
                    array_shift($data);
                }
                foreach ($data as $fields) {
                    foreach ($fields as $field => $val) {
                        if (isset($requiredFields[$field]) && !$val) {
                            $error[$field] = $fieldMessage[$field];
                        }
                    }
                }
            }

            if ($error) {
                $errors[$one['good_id']] = array_values($error);
            }
        }

        if ($errors) {
            foreach ($errors as $goodsId => $errMsg) {
                $upData = [
                    'check_error_msg' => serialize($errMsg),
                    'gmt_modified' => $gmt,
                ];
                Product::updateAll($upData, ['id' => $goodsId]);
            }
        }

        return $errors;
    }

    private function _checkProductIds(array $productIds, array $condition = [])
    {

        $query = Product::find()->select('id')->where(['id' => $productIds, 'is_deleted' => 'N']);
        if ($condition) {
            foreach ($condition as $one) {
                $query->andWhere($one);
            }
        }

        $products = $query->asArray()->all();

        $validProductIds = array_column($products, 'id');
        $errorProductIds = array_diff($productIds, $validProductIds);

        return [$validProductIds, $errorProductIds];
    }

    /**
    * [主要用于 定时任务（把处理中的产品发布到平台上）的查询]
    */
    public function getDealingGoodsList(array $params = array())
    {
        $pubStatus = isset($params['status']) ? intval(trim($params['status'])) : null;
        $shopIds = isset($params['shopIds']) ? $params['shopIds'] : array();
        $dealingStatus = isset($params['dealingStatus']) ? $params['dealingStatus'] : array();

        $query = Product::find()
            ->where(['is_deleted' => 'N']);

        if ($pubStatus !== null) {
            $query->andWhere(['pub_status' => $pubStatus]);
        }
        if ($shopIds !== null) {
            $query->andWhere(['in','shop_id',$shopIds]);
        }
        if ($dealingStatus !== null) {
            $query->andWhere(['dealing_status' => $dealingStatus]);
        }

        // die($query->createCommand()->getRawSql());

        if(!$query->count()){
            return null;
        }
        $products = $query->orderBy('id asc')
            ->asArray()
            ->all();
        return $products;

    }


    public function findById($id)
    {
          $product = Product::find()
            ->where(['id' => $id,'is_deleted' => 'N'])
            ->asArray()
            ->one();
            if($product){
                return $product;
            }

        return null;
    }

    public function updatePubStatus($id,$pubStatus){
        
        $product = Product::find()
            ->where(['id' => $id,'is_deleted' => 'N'])
            ->one();
        if(!$product){
            return null;
        }
        $product->pub_status = $pubStatus;
        $product->gmt_modified = date("Y-m-d H:i:s");
        return $product->save();
    }

}


?>