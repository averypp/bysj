<?php

namespace app\models;
use Yii;
use yii\db\ActiveRecord;
class Category extends ActiveRecord 
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sea_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['store_id', 'store_name', 'user_id','platform_id','site_id'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
           // 'store_id' => 'store_id',
           // 'store_name' => 'store_name',
           // 'user_id' => 'user_id',
        	//'platform_id'   => 'platform_id',
           // 'site_id' => 'site_id',
        ];
    }
    
	/**
     * @新建店铺信息存储
     * @param  array  $data
     * @return int  店铺id
     */
    public static  function saveCate($data)
    {   
        $category = new Category();
        $category->cat_id = $data['id'];
        $category->pid = $data['pid'];
        $category->cate_name = $data['name'];
        $category->level = $data['level'];
        $category->leaf = $data['leaf'];
        $category->platform_id = $data['platform_id'];
        $category->firstcat_id = $data['firstcat_id'];
        if($id=$category->save()){
            return $id;
        }
        return  null;
    }

    


}
