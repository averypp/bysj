<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;
use yii\db\ActiveRecord;
use yii\data\Pagination;
use yii\widgets\LinkPager;

class FollowsellerDetail extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sea_followseller_detail';
    }


    public function getDetailList($monitorId, $userId, $pageSize = 30, $oldList = false, $orderBy = "id desc")
    {
        $monitor = Monitor::findOne(['id' => $monitorId, 'is_deleted' => 'N']);
        if (!$monitor || $monitor->user_id != $userId) {
            return false;
        }

        $lists = $exclude_seller = [];
        
        $query = FollowsellerDetail::find()->where(['monitor_id' => $monitorId, 'is_deleted' => 'N']);
        if(strlen($monitor->exclude_seller)){
            $exclude_seller = explode(",", $monitor->exclude_seller);
            $query->andWhere(['not in', 'seller_id', $exclude_seller]);
        }
        if ($oldList) {
            $query->andFilterWhere(['>', 'follow_sell_end_at', 0]);
        } else {
            $query->andFilterWhere(['follow_sell_end_at' => 0]);
        }

        $pages = new Pagination([
            'defaultPageSize' => $pageSize,
            'totalCount' => $query->count(),
        ]);
        $pageString = LinkPager::widget([
            'pagination' => $pages,
            'options' => [
                'class' => 'pagination page-bar',
            ],
            'hideOnSinglePage' => false,
            // 'nextPageLabel' => '下一页',
            // 'prevPageLabel' => '上一页',
            // 'firstPageLabel' => '首页', 
            // 'lastPageLabel' => '尾页', 
        ]);

        $query->offset($pages->offset)->limit($pages->limit);
        if($orderBy){
            $lists['detail'] = $query->orderBy($orderBy)->asArray()->all();
        }else{
            $lists['detail'] = $query->orderBy('last_monitor_at desc,follow_sell_at asc')->asArray()->all();
        }
        $lists['monitor'] = [
            'item_name' => $monitor->item_name,
            'image_url' => $monitor->image_url,
            'asin' => $monitor->asin,
            'country' => $monitor->country,
        ];

        $lists['pageString'] = $pageString;
        $lists['totalCount'] = $pages->getPageCount();

        return $lists;
    }


    public static function multiSaveDetail($monitorData, $monitorId)
    {
        $datas = [];
        $_now = time();
        foreach ($monitorData as $key => $value) {
            $one = [
                'monitor_id' => $monitorId,
                'seller_name' => $value['sellerName'],
                'seller_id' => $value['sellerId'],
                'price' => $value['price'],
                'shopping_fee' => $value['shipFree'],
                'isFBA' => $value['isFBA'],
                'follow_sell_at' => time(),
                'last_monitor_at' => time(),
                'gmt_create' => date('Y-m-d H:i:s', $_now),
                'gmt_modified' => date('Y-m-d H:i:s', $_now),
            ];
            $datas[] = $one;
        }

        return Yii::$app->db->createCommand()->batchInsert(FollowsellerDetail::tableName(), array_keys($datas[0]), array_values($datas))->execute();
    }

    //存储跟卖卖家信息
    function recordSellerDetail($monitorData, $monitorId, $sellerCount, $amazonSellerCount)
    {
        if(!$monitorData || !$monitorId){
            return null;
        }
        $info = FollowsellerDetail::findAll(['monitor_id' => $monitorId, 'is_deleted' => 'N', 'follow_sell_end_at' => 0]);

        if ( ! $info ) {
            return self::multiSaveDetail($monitorData, $monitorId);
        }

        $oldSellerIds = [];
        foreach ($info as $k => $v) {
            $oldSellerIds[$v['id']] = $v['seller_id'];
        }
        $newSellerIds = array_keys($monitorData);

        $endSellerIds = array_diff($oldSellerIds, $newSellerIds);
        if ($endSellerIds) {
            FollowsellerDetail::updateAll(['follow_sell_end_at' => time()], ['id' => array_keys($endSellerIds)]);
        }

        $insertData = array_diff_key($monitorData, array_flip($oldSellerIds));
        if ($insertData) {
            self::multiSaveDetail($insertData, $monitorId);
        }

        // send sms
        $is_send = false;
        if ($insertData || $amazonSellerCount < $sellerCount) {
            fwrite(STDOUT, date('Y-m-d H:i:s') . "\tsend sms \n");
            $is_send = true;
        }

        $interIds = array_intersect($oldSellerIds, $newSellerIds);
        if ($interIds) {
            foreach ($interIds as $id => $sellerId) {
                $value = $monitorData[$sellerId];

                $updata = [
                    'monitor_id' => $monitorId,
                    'seller_name' => $value['sellerName'],
                    'seller_id' => $value['sellerId'],
                    'price' => $value['price'],
                    'shopping_fee' => $value['shipFree'],
                    'isFBA' => $value['isFBA'],
                    'last_monitor_at' => time(),
                    'gmt_modified' => date('Y-m-d H:i:s', time()),
                ];

                FollowsellerDetail::updateAll($updata, ['id' => $id]);
            }
        }

        return $is_send;
    }

    

}