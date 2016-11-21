<?php
namespace app\models;
use Yii;
use yii\web\IdentityInterface;
use yii\db\ActiveRecord;

use app\help;

/**
* [社区] 的数据层
* add by echo 2016-06-13
*/
class Community extends \yii\db\ActiveRecord
{


	public static function tableName()
    {
        return 'sea_community';

    }

    /**
     * @新建社区信息存储
     * @param  array  $data
     * @return 社区信息
     */
    public static  function createCommunity($data)
    {
        $community = new Community();
        $community->gmt_create = date("Y-m-d H:i:s");
        $community->gmt_modified = $community->gmt_create;
        $community->creator = $data['userId'];
        $community->modifier = $data['userId'];
        $community->name = $data['name'];
        $community->content = $data['content'];
        $community->weight = $data['weight'];
        if($community->save()){
            $id = $community->attributes['id'];//数据保存后返回插入的ID
            $community->id = $id;
            return $community;
        }
        return null;
    }

    /**
    * 逻辑删除 公告信息
    * @param id (公告ID) userId(操作人ID)
    * @return Boolean: TRUE 成功 / FALSE 失败
    */
    public static  function delCommunity($id,$userId)
    {
        $community = Community::findOne($id);
        if(empty($community)){//查无数据 则直接返回
             $message = "删除社区失败:查无此数据";
             return SeaShellResult::error($message);
        }
        $community->is_deleted = "Y";
        $community->modifier = $userId;
        $community->gmt_modified = date("Y-m-d H:i:s");
        if($community->update()!== false){
            //update success
            $message = "删除社区信息成功";
            return SeaShellResult::success($message);
        }
        $message = "删除社区信息失败";
        return SeaShellResult::error($message);

    }

    /**
     * @修改社区信息
     * @param  array  $data
     * @return Boolean: TRUE 成功 / FALSE 失败
     */
    public static  function editCommunity($data)
    {
        $community = Community::findOne($data['id']);
        if(empty($community)){//查无数据 则直接返回
             $message = "修改社区失败:查无此数据";
             return SeaShellResult::error($message);
        }
        $community->gmt_modified = date("Y-m-d H:i:s");
        $community->id = $data['id'];
        $community->modifier = $data['userId'];
        if(!empty($data['name'])){
            $community->name = $data['name'];
        }
        if(!empty($data['content'])){
            $community->content = $data['content'];
        }
        if(!empty($data['weight'])){
            $community->weight = $data['weight'];
        }
        if($community->update()!== false){
            //update success
            $message = "修改社区信息成功";
            return SeaShellResult::success($message);
        }
        $message = "修改社区信息失败";
        return SeaShellResult::error($message);
    }

    public static function getCommunitys()
    {
        $communitys = Community::find()->orderBy('weight desc')->asArray()->all();
        
        return $communitys;
    }


}


?>