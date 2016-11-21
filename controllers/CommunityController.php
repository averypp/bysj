<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\data\Pagination;

use app\models\Community;
use app\models\SeaShellResult;

/*
* [社区] 入口 文件 by echo add 2016-06-12
* 所有的方法 都未做数据安全校验/权限限制 /日志记录 后续待完善
*
*/
class CommunityController extends Controller{

     /*
    * [查询接口一：分页查询社区，默认显示10条] by echo add 2016-06-13 (已自测)
    */
    function actionList(){
        $pageSize = Yii::$app->request->get('size');//前端传入每页显示多少条数据;
        if(empty($pageSize)){
            $pageSize = 10;
        }
        $query = Community::find()
            ->where(['is_deleted' => 'N']);
        $page = new Pagination([
            'defaultPageSize' => $pageSize,
            'totalCount' => $query->count(),
        ]);
        $communities = $query->orderBy('weight desc,id desc')
            ->offset($page->offset)
            ->limit($page->limit)
            ->asArray()
            ->all();

        print_r($communities);
        die();

         //这里没有 list的视图
        return $this->render('list', [
            'communities' => $communities,
            'page' => $page,
        ]);
    }

    /*
    * [新增接口二：添加社区] by echo add 2016-06-13 (已自测)
    */
    function actionAdd(){
        $data['name'] = Yii::$app->request->get('name');//社区标题
        $data['content'] = Yii::$app->request->get('content');//社区内容
        $data['weight'] = Yii::$app->request->get('weight');//社区权重
        $data['userId'] = Yii::$app->request->get('uid');//当前操作人
        if( empty($data['name']) || empty($data['content']) || empty($data['userId']) || empty($data['weight']) ){
            $message = "请传入[标题、内容、操作人ID、社区权重]";
            return SeaShellResult::errorInfo($message);
        }

        $communityModel = new Community();
        print_r($data);
        $community=$communityModel->createCommunity($data);

        if(!$community){
            $message = "新增社区失败";
            return SeaShellResult::errorInfo($message);
        }
        return SeaShellResult::arrayToJson($community);
    }

    /*
    * [编辑接口三：修改社区] by echo add 2016-06-13 (已自测)
    */
    function actionEdit(){
        $data['id'] = Yii::$app->request->get('id');//前端传入社区ID;
        $data['name'] = Yii::$app->request->get('name');//社区标题
        $data['content'] = Yii::$app->request->get('content');//社区内容
        $data['weight'] = Yii::$app->request->get('weight');//社区权重
        $data['userId'] = Yii::$app->request->get('uid');//当前操作人
        if( empty($data['id']) ){
            $message = "请传入[社区ID]";
            return SeaShellResult::errorInfo($message);
        }

        $communityModel = new Community();
        $editResult=$communityModel->editCommunity($data);
        return $editResult;
    }

    /*
    * [删除接口四：逻辑删除] by echo add 2016-06-13 (已自测)
    */
    function actionDel(){
        $id = Yii::$app->request->get('id');//前端传入社区ID;
        $userId = Yii::$app->request->get('uid');//前端传入当前操作人ID;
        if( empty($id) || empty($userId) ){
            $message = "请传入[社区ID、操作人ID]";
            return SeaShellResult::errorInfo($message);
        }
        $communityModel = new Community();
        $delResult = $communityModel->delCommunity($id,$userId);
        return $delResult;
    }

}


?>