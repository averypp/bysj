<?php

namespace app\models;

use Yii;
use app\models\Bidding;
use app\models\BiddingRulesItem;
use app\models\BiddingRulesType;
class BiddingRules extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sea_bidding_rules';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'competitors', 'create_at', 'shop_id'], 'required'],
            [['buybox_set', 'create_at', 'update_at', 'shop_id'], 'integer'],
            [['buybox_set_value1', 'buybox_set_value2'], 'number'],
            [['buybox_set_math1', 'buybox_set_math1', 'buybox_item'], 'string'],
            [['name'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 500],
            [['competitors'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'buybox_set' => 'Buybox Set',
            'buybox_set_value1' => 'Buybox Set Value1',
            'buybox_set_value2' => 'Buybox Set Value2',
            'buybox_set_math' => 'Buybox Set Math',
            'buybox_item' => 'Buybox Item',
            'competitors' => 'Competitors',
            'create_at' => 'Create At',
            'update_at' => 'Update At',
        ];
    }

    public function getTypes()
    {
        return $this->hasMany(BiddingRulesType::className(), ['rules_id' => 'id']);
    }
    public function getItems()
    {
        return $this->hasMany(BiddingRulesItem::className(), ['rules_id' => 'id']);
    }

    /**
     * 例子获取规则所有的相关信息
     * @return array
     */
    public static function example()
    {
        return BiddingRules::find()->with(['types', 'types.items'])->asArray()->all();
    }

    public static function removeRule($ruleId, $shopId)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $ret = BiddingRules::deleteAll(['id' => $ruleId, 'shop_id' => $shopId]);
            if (!$ret) {
                throw new \Exception("delete rules failed.");
            }
            BiddingRulesItem::deleteAll(['rules_id' => $ruleId]);
            BiddingRulesType::deleteAll(['rules_id' => $ruleId]);
            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }

    public static function recordInfo($ruleInfo)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $ruleId = BiddingRules::insertRules($ruleInfo);
            if(!$ruleId){
                $transaction->rollBack();
                echo ' create ruleId error';
                return false;
            }
            if( !BiddingRulesType::insertRulesType($ruleInfo, $ruleId) ){
                $transaction->rollBack();
                echo ' insertRulesType function error';
                return false;
            }
            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            echo $e->getMessage();
            return false;
        }
    }

    public static function editRulesInfo($ruleInfo, $shopId)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $ruleId = BiddingRules::insertRules($ruleInfo, $shopId);
            if(!$ruleId){
                $transaction->rollBack();
                echo ' create ruleId error';
                return false;
            }
            $existRuleInfo = BiddingRules::find()->with(['types', 'types.items'])->asArray()->one();
            //var_dump($ruleInfo);die;
            //var_dump($existRuleInfo);die;
            if( !BiddingRulesType::updateRulesType($ruleInfo, $ruleId, $existRuleInfo) ){
                $transaction->rollBack();
                echo ' update RulesType function error';
                return false;
            }
            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            echo $e->getMessage();
            echo $e->getLine();
            return false;
        }
    }

    public static function insertRules($ruleInfo, $shopId = 0)
    {
        if($ruleInfo['rule-id']){
            $BiddingRulesModel = BiddingRules::findOne(['id' => $ruleInfo['rule-id'], 'shop_id' => $shopId]);
            if (!$BiddingRulesModel) {
                throw new \Exception("操作异常");
            }
            $BiddingRulesModel->update_at = time();
            //BiddingRulesItem::deleteAll(['rules_id' => $ruleInfo['rule-id']]);
            //BiddingRulesType::deleteAll(['rules_id' => $ruleInfo['rule-id']]);
        } else {
            $BiddingRulesModel = new BiddingRules();
            $BiddingRulesModel->create_at = time();
        }
        $BiddingRulesModel->name = $ruleInfo['rule-name'];
        $BiddingRulesModel->shop_id = $ruleInfo['shopId'];
        $BiddingRulesModel->description = $ruleInfo['rule-description'];
        $BiddingRulesModel->buybox_set = $ruleInfo['buybox']['buybox_set'];
        $BiddingRulesModel->buybox_set_value1 = $ruleInfo['buybox']['buybox_set_value1'] ? : 0;
        $BiddingRulesModel->buybox_set_value2 = $ruleInfo['buybox']['buybox_set_value2'] ? : 0;
        $BiddingRulesModel->buybox_set_math1 = $ruleInfo['buybox']['buybox_set_math1'];
        $BiddingRulesModel->buybox_set_math2 = $ruleInfo["buybox"]['buybox_set_math2'];
        $BiddingRulesModel->buybox_item = $ruleInfo['buybox']['buybox_item'];
        $BiddingRulesModel->competitors = implode(',', $ruleInfo['competitors']);
        if($ruleInfo['rule-id']){
            $BiddingRulesModel->save();
            return $ruleInfo['rule-id'];
        } else {
            $BiddingRulesModel->save();
            return $BiddingRulesModel->primaryKey;
        }
    }
}
