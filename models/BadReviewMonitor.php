<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use app\models\BadReview;

class BadReviewMonitor extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sea_bad_review_monitor';
    }

    public function getUserMonitor($user_id){
    	$monitors = BadReviewMonitor::find( [ 'user_id' => $user_id ] )->asArray()->all();
    	return $monitors;
    }

    public function getReviews()
    {
        return $this->hasMany(BadReview::className(), ['monitor_id' => 'id']);
    }

    public function addAsin($asin, $userId){
    	$monitor_model = BadReviewMonitor::findOne(['asin'=>$asin, 'user_id'=>$userId]);
        if( $monitor_model ){
        	return true;
        }
        $monitor_model = new BadReviewMonitor();
        $monitor_model->asin = $asin;
        $monitor_model->user_id = $userId;
        $monitor_model->create_at = time();
        return $monitor_model->save();
    }

    public function delMonitor($id,$userId){
    	$transaction = Yii::$app->db->beginTransaction();
        try {
            
            $del = BadReviewMonitor::findOne(['id'=>$id, 'user_id'=>$userId])->delete();

            $delall = BadReview::deleteAll(['monitor_id' => $id]);

            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }

    public function readReview($user_id){
        if($user_id){
            return BadReviewMonitor::updateAll(['is_read' => 1, 'review_total' => 0], ['user_id'=>$user_id]);
        }
        return false;
    }

}