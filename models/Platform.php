<?php

namespace app\models;
use Yii;
use yii\db\ActiveRecord;

use yii\helpers\ArrayHelper;

class Platform extends \yii\db\ActiveRecord 
{
    
    /**
     * @inheritdoc
     */
    public  static function tableName()
    {
        return 'sea_plat_form';
    }

    /**
     * @inheritdoc
     */
    public  function rules()
    {
        return [
            [[ 'pid', 'platform_name'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public  function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pid' => '平台ID',
            'platform_name' => '平台名称',
        ];
    }


    /**
     * 查找单条信息
     *
     * @param  string      $id
     * @return array or null
     */
    public  static function findById($id)
    {
    	  $Platform = new Platform();
          $plat_info = $Platform::find()
            ->where(['id' => $id])
            ->asArray()
            ->one();
            if($plat_info){
                return $plat_info;
            }

        return null;
    }


    /**
     * 查找父级id下的所有子级站点
     *
     * @param  string      $id
     * @return static|null
     */
    public  static function findByPid($pid)
    {
    	 // $Platform = new Platform();
          //$plat_info = $Platform
          //  ->findAll(array(
		//	  'select' =>array('platform_name','id'),
		//	));
		$plat_info = Platform::find()->select(['id', 'platform_name'])->where(['pid' => $pid])->all();
			
			//$plat_info = Platform::model()->findAll(array(
			 // 'select' =>array('platform_name','id'),
			//  'order' => 'id DESC', 
			//));
            if($plat_info){
                return $plat_info;
            }

        return null;
    }

    public function getSiteByPlatform($platform)
    {
        $platform = trim($platform);

        if (!$platform) {
            return [];
        }

        $query = Platform::find();

        $siteInfo = $query->select(['p.id', 'p.platform_name as name'])
            ->where(['sea_plat_form.is_deleted' => 'N'])
            ->andWhere(['p.is_deleted' => 'N'])
            ->andWhere(['sea_plat_form.platform_name' => $platform])
            ->leftJoin('sea_plat_form as p', 'p.pid = sea_plat_form.id')
            ->asArray()
            ->all();

        return $siteInfo;

    }

    public function getPlatformIdByName($platformName)
    {
        $platformId = 0;

        $platformName = trim($platformName);
        if (!$platformName) {
            return $platformId;
        }

        $platform = Platform::find()->where(['platform_name' => $platformName])->andwhere(['pid' => 0])->one();
        if ($platform) {
            $platformId = $platform->id;
        }

        return $platformId;

    }

    public function isValidPlatformSite($platformName, $siteId)
    {
        if (!$platformName || !$siteId) {
            return false;
        }

        $site = Platform::find()->where(['id' => $siteId])->andWhere(['>', 'pid', 0])->one();
        if (!$site) {
            return false;
        }

        $platform = Platform::findOne($site->pid);
        if (!$platform || $platform->platform_name != $platformName) {
            return false;
        }

        return $site;

    }

    /**
     * 查找多条信息
     *
     * @param  string      $ids
     * @return array or null
     */
    public  static function findByIds(array $ids)
    {
        if(empty($ids)){
            return null;
        }
        $Platform = new Platform();
        $plat_infos = $Platform::find()
            ->where(['in','id',$ids])
            ->asArray()
            ->all();
        if($plat_infos){
            return $plat_infos;
        }
        return null;
    }
     
}
