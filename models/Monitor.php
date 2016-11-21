<?php

namespace app\models;
use Yii;
use yii\web\IdentityInterface;
use yii\db\ActiveRecord;
use app\models\FollowsellerDetail;

use yii\helpers\ArrayHelper;

class Monitor extends ActiveRecord 
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sea_followseller_monitor';  // '{{user}}';

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
    //抓取跟卖信息时，更新monitor表信息
    function saveMonitor($mainData, $monitor_id)
    {
        $monitor_model = Monitor::findOne($monitor_id);
        if (!$monitor_model) {
            return false;
        }

        if (isset($mainData['title'])) {
            $monitor_model->item_name = $mainData['title'];
        }
        if (isset($mainData['image'])) {
            $monitor_model->image_url = $mainData['image'];
        }
        // $monitor_model->buybox_seller = $mainData['buybox_seller'];
        $monitor_model->amazon_seller_count = isset($mainData['amazon_seller_count']) ? $mainData['amazon_seller_count'] : $monitor_model->amazon_seller_count;
        $monitor_model->fba_count = isset($mainData['fba_count']) ? $mainData['fba_count'] : $monitor_model->fba_count;
        $monitor_model->seller_count = isset($mainData['seller_count']) ? $mainData['seller_count'] : $monitor_model->seller_count;
        $monitor_model->low_price = isset($mainData['low_price']) ? $mainData['low_price'] : $monitor_model->low_price;
        $monitor_model->gmt_modified = date('Y-m-d H:i:s', time());
        $monitor_model->last_monitor_at = time();
        $monitor_model->save();
    }
    //跟卖信息抓取完成时，更新monitor表信息
    // function updateMonitor($monitorId)
    // {
    //     $monitor_model = Monitor::findOne(['id'=>$monitorId]);
    //     $FollowsellerDetailInfo = FollowsellerDetail::findAll(['monitor_id'=>$monitorId]);
    //     $fbaCount= 0;
    //     foreach ($FollowsellerDetailInfo as $key => $value) {
    //         if($value['isFBA']){
    //             $fbaCount+=1;
    //         }
    //     }
    //     $monitor_model->seller_count = count($FollowsellerDetailInfo);
    //     $monitor_model->fba_count = $fbaCount;
    //     $monitor_model->save();
    // }

    function findMonitorIdByAsin($asin){
        $monitor_model = Monitor::findOne(['asin'=>$asin]);
        if($monitor_model){
            return $monitor_model->id;
        } else {
            return null;
        }
    }

    function getMonitorInfo($limit = 20)
    {
        $_now = time();
        $query = Monitor::find()->select(['id', 'last_monitor_at', 'asin', 'user_id'])->where(['is_deleted' => 'N', 'is_monitor' => 1])
            ->andWhere(['<=', 'last_monitor_at', $_now - 3600])->limit($limit);
        $info = $query->asArray()->all();

        if ($info) {
            $ids = array_column($info, 'id');
            Monitor::updateAll(['last_monitor_at' => $_now], ['id' => $ids]);
        }

        return $info;
    }

    //add asin
    function addAsin($asin, $user_id){
        $monitor_model = Monitor::findOne(['is_deleted'=>'N', 'asin'=>$asin, 'user_id'=>$user_id]);
        if( !$monitor_model ){
            $monitor_model = new Monitor();
            $monitor_model->user_id = $user_id;
            $monitor_model->asin = $asin;
            $monitor_model->gmt_create = date('Y-m-d H:i:s', time());
            return $monitor_model->save();
        }else{
            $monitor_model->gmt_create = date('Y-m-d H:i:s', time());
            $monitor_model->gmt_modified = date('Y-m-d H:i:s', time());
            return $monitor_model->save();
        }
        return false;
    }

    //modify exclude_seller
    function modifyExcludeseller($id, $seller,$userId){
        $monitor_model = Monitor::findOne(['id'=>$id]);
            
        if($monitor_model){
            $monitor_model->exclude_seller = $seller;
            $monitor_model->modifier = $userId;
            $monitor_model->gmt_modified = date('Y-m-d H:i:s', time());
            return $monitor_model->save();
        }
        return false;
    }

    //cancel monitor
    function cancelMonitor($id,$userId){
        $monitor_model = Monitor::findOne(['id'=>$id]);
        if($monitor_model){
            $monitor_model->is_monitor = 0;
            $monitor_model->last_monitor_at = 0;
            $monitor_model->low_price = 0;
            $monitor_model->modifier = $userId;
            $monitor_model->gmt_modified = date('Y-m-d H:i:s', time());
            return $monitor_model->save();
        }
        return false;
    }

    //open monitor
    function openMonitor($id,$userId){
        $monitor_model = Monitor::findOne(['id'=>$id]);
        if($monitor_model){
            $monitor_model->is_monitor = 1;
            $monitor_model->modifier = $userId;
            $monitor_model->gmt_modified = date('Y-m-d H:i:s', time());
            return $monitor_model->save();
        }
        return false;
    }

    //del monitor
    function delMonitor($id,$userId){
        $monitor_model = Monitor::findOne(['id'=>$id]);
        if(empty($monitor_model)){
            return false;
        }
        $monitorDetailModel = FollowsellerDetail::find()->where(['monitor_id' => $id, "is_deleted" =>"N"])->all();
        $updateMonitorDetailResult = true;
            
        if(!empty($monitorDetailModel)){
            $_time = date('Y-m-d H:i:s', time());
            $monitor_model->is_monitor = 0;
            $monitor_model->low_price = 0;
            $monitor_model->is_deleted = "Y";
            $monitor_model->modifier = $userId;
            $monitor_model->gmt_modified = $_time;
            
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if(!$monitor_model->save()){
                    echo "<<<逻辑删除跟卖主表信息异常>>>";
                    throw new Exception("逻辑删除跟卖主表信息异常 ", 1);
                }
                if(!FollowsellerDetail::updateAll(["is_deleted" => "Y", "gmt_modified" => $_time], ["monitor_id" => $id])){
                    echo "逻辑删除跟卖详情异常";
                    throw new Exception("逻辑删除跟卖详情异常 ", 1);
                }
                $transaction->commit();
                return true;
            } catch (Exception $e) {
                $transaction->rollBack();
                echo "本地保存接口 异常：".$e;
                return false;
            }
            
        }else{
            $monitor_model->is_monitor = 0;
            $monitor_model->low_price = 0;
            $monitor_model->is_deleted = "Y";
            $monitor_model->modifier = $userId;
            $monitor_model->gmt_modified = date('Y-m-d H:i:s', time());
            return $monitor_model->save();
        }
        
        return false;
    }


}
