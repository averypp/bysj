<?php
namespace app\models;
use Yii;
use yii\web\IdentityInterface;
use yii\db\ActiveRecord;

/**
* [公告] 的数据层
* add by echo 2016-06-13
*/
class Notice extends \yii\db\ActiveRecord
{


	public static function tableName()
    {
        return 'sea_notice';
    }

    /**
     * @新建公告信息存储
     * @param  array  $data
     * @return 公告信息
     */
    public static  function createNotice($data)
    {
        $notice = new Notice();
        $notice->gmt_create = date("Y-m-d H:i:s");
        $notice->gmt_modified = $notice->gmt_create;
        $notice->creator = $data['userId'];
        $notice->modifier = $data['userId'];
        $notice->name = $data['name'];
        $notice->content = $data['content'];
        if($notice->save()){
            $id = $notice->attributes['id'];//数据保存后返回插入的ID
            $notice->id = $id;
            return $notice;
        }
        return null;
    }

    /**
    * 逻辑删除 公告信息
    * @param id (公告ID) userId(操作人ID)
    * @return Boolean: TRUE 成功 / FALSE 失败
    */
    public static  function delNotice($id,$userId)
    {
        $notice = Notice::findOne($id);

        $notice->is_deleted = "Y";
        $notice->modifier = $userId;
        $notice->gmt_modified = date("Y-m-d H:i:s");
        return $notice->save();
    }

    public static function getNewNotices($limit = 10)
    {

        $limit = abs(intval($limit)) ?: 10;

        $notices = Notice::find()
            ->orderBy('gmt_create desc')
            ->asArray()
            ->limit($limit)
            ->all();

        foreach ($notices as $k => &$v) {
            $v['content'] = explode('|', $v['content']);
        }

        return $notices;

    }


}


?>