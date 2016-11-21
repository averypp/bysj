<?php

namespace app\models;

use Yii;

class BiddingRulesItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sea_bidding_rules_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rules_id', 'type_id', 'create_at', 'compare'], 'required'],
            [['rules_id', 'type_id', 'create_at', 'update_at'], 'integer'],
            [['symbol', 'math', 'compare', 'options', 'item'], 'string'],
            [['value'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'rules_id' => 'Rules ID',
            'type_id' => 'Type ID',
            'symbol' => 'Symbol',
            'value' => 'Value',
            'math' => 'Math',
            'compare' => 'Compare',
            'options' => 'Options',
            'item' => 'Item',
            'create_at' => 'Create At',
            'update_at' => 'Update At',
        ];
    }
    public static function insertRulesItem($k, $v, $ruleId, $typeId)
    {
        $BiddingRulesItemModel = new BiddingRulesItem();
        $BiddingRulesItemModel->rules_id = $ruleId;
        $BiddingRulesItemModel->type_id = $typeId;
        $BiddingRulesItemModel->compare = $k;

        $options = isset($v['options']) ? $v['options'] : null;
        $BiddingRulesItemModel->options = $options;
        /*
            options与item不能同时为null
            当option等于customize或null时,item不能为null
         */
        if ('customize' == $options || $options === null) {
            if (!isset($v['item'])) {
                return false;
            }
            $BiddingRulesItemModel->symbol = isset($v['symbol']) ? $v['symbol'] : '-';
            $BiddingRulesItemModel->value = isset($v['value']) ? $v['value'] : 0.01;
            $BiddingRulesItemModel->math = isset($v['math']) ? $v['math'] : '$';
            $BiddingRulesItemModel->item = $v['item'];
        }
        $BiddingRulesItemModel->create_at = time();
        $BiddingRulesItemModel->update_at = time();
        if( $BiddingRulesItemModel->save() ){
            return $BiddingRulesItemModel->primaryKey;
        } else {
            return false;
        }
    }

    public static function updateRulesItem($id, $itemInfo)
    {
        $BiddingRulesItemModel = BiddingRulesItem::findOne(['id' => $id]);
        $options = isset($itemInfo['options']) ? $itemInfo['options'] : null;
        $BiddingRulesItemModel->options = $options;
        /*
            options与item不能同时为null
            当option等于customize或null时,item不能为null
         */
        if ('customize' == $options || $options === null) {
            if (!isset($itemInfo['item'])) {
                return false;
            }
            $BiddingRulesItemModel->symbol = isset($itemInfo['symbol']) ? $itemInfo['symbol'] : '-';
            $BiddingRulesItemModel->value = isset($itemInfo['value']) ? $itemInfo['value'] : 0.01;
            $BiddingRulesItemModel->math = isset($itemInfo['math']) ? $itemInfo['math'] : '$';
            $BiddingRulesItemModel->item = $itemInfo['item'];
        }
        if( isset($itemInfo['is_special']) ){
            $BiddingRulesItemModel->item = null;
            $BiddingRulesItemModel->value = null;
            $BiddingRulesItemModel->math = null;
            $BiddingRulesItemModel->symbol = null;
        }
        $BiddingRulesItemModel->update_at = time();
        if( $BiddingRulesItemModel->save() ){
            return true;
        } else {
            return false;
        }
    }

    public function getType()
    {
        return $this->hasOne(BiddingRulesType::className(), ['id' => 'type_id']);
    }
}
