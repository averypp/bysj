<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\data\Pagination;

use app\models\Notice;
use app\models\SeaShellResult;

/*
* [公告] 入口 文件 by echo add 2016-06-12
* 所有的方法 都未做数据安全校验/权限限制 /日志记录 后续待完善
*
*/
class NoticeController extends Controller{

    /*
    * [查询接口一：分页查询公告，默认显示10条] by echo add 2016-06-13 (已自测)
    */
    function actionList(){
        $pageSize = Yii::$app->request->get('size');//前端传入每页显示多少条数据;
        if(empty($pageSize)){
            $pageSize = 10;
        }
        $query = Notice::find()
            ->where(['is_deleted' => 'N']);
        $page = new Pagination([
            'defaultPageSize' => $pageSize,
            'totalCount' => $query->count(),
        ]);
        $notices = $query->orderBy('id')
            ->offset($page->offset)
            ->limit($page->limit)
            ->all();
        print_r($notices);
        die();
         //这里没有 list的视图
        return $this->render('list', [
            'notices' => $notices,
            'page' => $page,
        ]);
    }

    /*
    * [新增接口二：添加公告] by echo add 2016-06-13 (已自测)
    */
    function actionAdd(){
        $data['name'] = Yii::$app->request->get('name');
        $data['content'] = Yii::$app->request->get('content');
        $data['userId'] = Yii::$app->request->get('uid');
        if(empty($data['name']) || empty($data['content']) || empty($data['userId'])){
            $message = "请传入[标题、内容、操作人ID]";
            return SeaShellResult::errorInfo($message);
        }

        $noticeModel = new Notice();
        print_r($data);
        $notice=$noticeModel->createNotice($data);

        if(!$notice){
            $message = "新增公告失败";
            return SeaShellResult::errorInfo($message);
        }
        return SeaShellResult::arrayToJson($store);
    }

    /*
    * [删除接口三：逻辑删除] by echo add 2016-06-13 (已自测)
    */
    function actionDel(){
        $id = Yii::$app->request->get('id');//前端传入公告ID;
        $userId = Yii::$app->request->get('uid');//前端传入公告ID;
        if( empty($id) || empty($userId) ){
            $message = "请传入[公告ID、操作人ID]";
            return SeaShellResult::errorInfo($message);
        }
        $noticeModel = new Notice();
        $delResult = $noticeModel->delNotice($id,$userId);
        //返回什么视图 todo

    }
}

?>