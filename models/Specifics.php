<?php

namespace app\models;
use Yii;
use yii\web\IdentityInterface;
use yii\db\ActiveRecord;
use app\models\GoodsPicture;
use app\models\GoodsSoldInfo;
class Specifics extends ActiveRecord 
	{
	
	

    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sea_specifics';  // '{{user}}';

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

    

    function createSpecifics($specifics_data)
    {
        
        $specifics_model = new Specifics();
        $specifics_model->cat_id = $specifics_data['cat_id'];
        $specifics_model->store_id = $specifics_data['store_id'];
        $specifics_model->site_id = $specifics_data['site_id'];
        $specifics_model->VariationTheme = isset($specifics_data['VariationTheme']) ? $specifics_data['VariationTheme'] : '';
        $specifics_model->Color = isset($specifics_data['Color']) ? $specifics_data['Color'] : '';
        $specifics_model->Size = isset($specifics_data['Size']) ? $specifics_data['Size'] : '';
        $specifics_model->LegStyle = isset($specifics_data['LegStyle']) ? $specifics_data['LegStyle'] : '';
        $specifics_model->MaterialType = isset($specifics_data['MaterialType']) ?  $specifics_data['MaterialType'] : '';
        $specifics_model->Department = isset($specifics_data['Department']) ? $specifics_data['Department'] : '';
        $specifics_model->save();
    }


     //check product type info with cat_id ,subcat_id 
    function getSpecifics($cat_id){
         $specifics_data= Specifics::find()
         ->where(['cat_id' => $cat_id])
         ->asArray()
         ->one();
        unset($specifics_data['id']); unset( $specifics_data['cat_id']); unset( $specifics_data['store_id']); unset( $specifics_data['site_id']);
        foreach ($specifics_data as $key => $value) {
            if($value){
                $specifics_data[$key]=explode(',', $value);

            }

        }
        return $specifics_data;

    }

}
