<?php

namespace app\models;

use Yii;

class BiddingRulesType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sea_bidding_rules_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'rules_id', 'create_at'], 'required'],
            [['rules_id', 'is_open', 'create_at', 'update_at'], 'integer'],
            [['type'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'rules_id' => 'Rulos ID',
            'is_open' => 'Is Open',
            'create_at' => 'Create At',
            'update_at' => 'Update At',
        ];
    }

    public function getItems()
    {
        return $this->hasMany(BiddingRulesItem::className(), ['type_id' => 'id']);
    }

    public static function insertRulesType($ruleInfo, $ruleId)
    {
        unset($ruleInfo["competitors"]);
        unset($ruleInfo["buybox"]);
        $transaction = Yii::$app->db->beginTransaction();
        foreach ($ruleInfo as $key => $value) {
            if(is_array($value)){
                if(isset($key) && !empty($value)){
                    $BiddingRulesTypeModel = new BiddingRulesType();
                    $BiddingRulesTypeModel->type = $key;
                    $BiddingRulesTypeModel->rules_id = $ruleId;
                    $BiddingRulesTypeModel->is_open = isset($value['is_open']) ? $value['is_open'] : 1;
                    $BiddingRulesTypeModel->create_at = time();
                    $BiddingRulesTypeModel->update_at = time();
                    $BiddingRulesTypeModel->save();
                    $typeId = $BiddingRulesTypeModel->primaryKey;
                    if(isset($value['is_open'])){
                        unset($value['is_open']);
                    }
                    foreach ($value as $k => $v) {
                        if( !BiddingRulesItem::insertRulesItem($k, $v, $ruleId, $typeId) ){
                            $transaction->rollBack();
                            echo "insertRulesItem  error";
                            return false;
                        }
                    }
                } else {
                    $transaction->rollBack();
                    echo "params error";
                    return false;
                }
            }
        }
        $transaction->commit();
        return true;
    }


    public static function updateRulesType($ruleInfo, $ruleId, $existRuleInfo)
    {
        $transaction = Yii::$app->db->beginTransaction();
        foreach ($existRuleInfo['types'] as $existKey => $existValue) {
            if(isset($existValue['type']) && is_array($existValue) && isset($ruleInfo[$existValue['type']]) ){
                $BiddingRulesTypeModel = BiddingRulesType::findOne(['id' => $existValue['id'] ]);
                $BiddingRulesTypeModel->is_open = isset($ruleInfo[$existValue['type']]['is_open']) ? $ruleInfo[$existValue['type']]['is_open'] : 1;
                $BiddingRulesTypeModel->update_at = time();
                if(!$BiddingRulesTypeModel->save()){
                    echo "update  RulesType table error";
                    return false;
                }
                /*if(isset($value['is_open'])){
                    unset($value['is_open']);
                }*/
                foreach ($existValue['items'] as $k => $item) {
                    //当所有竞争者都低于最小价格 & 高于最大价格,options 和item 判断替换
                    if($existValue['type'] == 'basic' && $item['compare'] == 'both' && $ruleInfo[$existValue['type']][$item['compare']]['options'] != 'stop'){
                        $ruleInfo[$existValue['type']][$item['compare']]['item'] = $ruleInfo[$existValue['type']][$item['compare']]['options'];
                        $ruleInfo[$existValue['type']][$item['compare']]['options'] = null;
                    }
                    if($existValue['type'] == 'basic' && $item['compare'] == 'both' && $ruleInfo[$existValue['type']][$item['compare']]['options'] == 'stop'){
                        $ruleInfo[$existValue['type']][$item['compare']]['is_special'] = true;
                    }
                    if( !BiddingRulesItem::updateRulesItem($item['id'], $ruleInfo[$existValue['type']][$item['compare']]) ){
                        $transaction->rollBack();
                        echo "update RulesItem table error";
                        return false;
                    }
                }
            } else {
                $transaction->rollBack();
                echo "params error";
                return false;
            }
        }
        $transaction->commit();
        return true;
    }
}
