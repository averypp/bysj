<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\data\Pagination;

use app\models\Store;
use app\models\SeaShellResult;

/*
* [店铺] 入口 文件 by echo add 2016-06-12
* 所有的方法 都未做数据安全校验/权限限制 /日志记录 后续待完善
*
*/
class StoreController extends Controller{

    /*
    * [查询接口一：根据userId获取店铺信息] by echo add 2016-06-12 (测试通过)
    * 返回json格式的SeaShellResult
    */
    function actionListByUid(){
        $uId = Yii::$app->request->get('uid');//当前平台的ID
        if( empty($uId) ){
            $message = "请传入 [UID]";
            return SeaShellResult::errorInfo($message);
        }
        $stores = Store::find()
            ->where(['is_deleted' => 'N','user_id' => $uId])
            ->all();
        return SeaShellResult::arrayToJson($stores);
    }

    /*
    * [查询接口二：分页查询店铺列表] by echo add 2016-06-12
    *
    */
    function actionList(){
        $pageSize = Yii::$app->request->get('size');//前端传入每页显示多少条数据;
        $query = Store::find()
            ->where(['is_deleted' => 'N']);
        $page = new Pagination([
            'defaultPageSize' => $pageSize,
            'totalCount' => $query->count(),
        ]);
        $stores = $query->orderBy('id')
            ->offset($page->offset)
            ->limit($page->limit)
            ->all();
         //这里没有 list的视图
        return $this->render('list', [
            'stores' => $stores,
            'page' => $page,
        ]);
    }

    /*
    * [修改接口三：主要是修改店铺名称] by echo add 2016-06-12 （测试ok）
    *
    */
    function actionEdit(){
        $id = Yii::$app->request->get('id');//店铺ID;
        $storeName = Yii::$app->request->get('name');//店铺名称;
        $userId = Yii::$app->request->get('userId');//userId(当前登录人ID，后续从seesion中获取);
        if(empty($id) || empty($storeName) || empty($userId)){
            $message = "请传入参数[店铺ID 和 店铺名称 和用户ID]";
            return SeaShellResult::errorInfo($message);
        }
        $store = Store::findOne($id);
        if($userId != $store->user_id ){
            $message = "非法操作 当前登录人无权编辑该店铺";
            return SeaShellResult::errorInfo($message);
        }
        if($storeName == $store->store_name){
            $message = "新的店铺名称与原来一致，不做更新操作";
            return SeaShellResult::errorInfo($message);
        }
        $store->store_name = $storeName;
        $store->modifier = $userId;
        $store->gmt_modified = date("Y-m-d H:i:s");
        $store->save();
        //返回什么视图 todo
    }

    /*
    * [删除接口四：主要是逻辑删除] by echo add 2016-06-12
    */
    function actionDeleted(){
        $id = Yii::$app->request->get('id');//店铺ID;
        $userId = Yii::$app->request->get('userId');//userId(当前登录人ID，后续从seesion中获取);
        if(empty($id) || empty($userId)){
            $message = "请传入参数[店铺ID 和用户ID]";
            return SeaShellResult::errorInfo($message);
        }
        $store = Store::findOne($id);
        if($userId != $store->user_id ){
            $message = "非法操作 当前登录人无权编辑该店铺";
            return SeaShellResult::errorInfo($message);
        }

        $store->is_deleted = "Y";
        $store->modifier = $userId;
        $store->gmt_modified = date("Y-m-d H:i:s");
        $store->save();
        //返回什么视图 todo

    }

    /*
    * [新增店铺接口五：新增店铺] by echo add 2016-06-12 (还未测试 todo)
    *
    */
    function actionSave(){
        $data['store_name'] = Yii::$app->request->post('na');
        $data['platform_id'] = Yii::$app->request->post('platform_id');
        $data['site_id'] = Yii::$app->request->post('sp');
        $data['merchant_id'] = Yii::$app->request->post('merchant_id');
        $data['accesskey_id'] = Yii::$app->request->post('accesskey_id');
        $data['secret_key'] = Yii::$app->request->post('secret_key');
        $store_model = new Store();
        $store=$store_model->createStore($data);
        $return_data['success'] = false;
        if(!$store){
            $message = "新建店铺失败";
            return SeaShellResult::errorInfo($message);
        }
        return SeaShellResult::arrayToJson($store);
    }























}


?>