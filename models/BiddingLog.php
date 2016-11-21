<?php

namespace app\models;

use Yii;

class BiddingLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sea_bidding_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['modify_at', 'goods_title', 'asin', 'sku', 'rules_name'], 'required'],
            [['modify_at', 'adjust_status'], 'integer'],
            [['mix_price', 'max_price'], 'number'],
            [['goods_title'], 'string', 'max' => 500],
            [['asin'], 'string', 'max' => 20],
            [['sku', 'rules_name'], 'string', 'max' => 60],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'modify_at' => 'Modify At',
            'goods_title' => 'Goods Title',
            'asin' => 'Asin',
            'sku' => 'Sku',
            'mix_price' => 'Mix Price',
            'max_price' => 'Max Price',
            'rules_name' => 'Rules Name',
            'adjust_status' => 'Adjust Status',
        ];
    }
    public function addBiddingLog($logData, $status)
    {
        $BiddingLogModel = new BiddingLog();
        $BiddingLogModel->modify_at = time();
        $BiddingLogModel->goods_title = $logData['goods_title'];
        $BiddingLogModel->asin = $logData['asin'];
        $BiddingLogModel->sku = $logData['sku'];
        $BiddingLogModel->mix_price = $logData['mix_price'];
        $BiddingLogModel->max_price = $logData['max_price'];
        $BiddingLogModel->rules_name = $logData['rules_name'];
        $BiddingLogModel->adjust_status = $status;
        if($BiddingLogModel->save()){
            return true;
        }
        return false;
    }
}
