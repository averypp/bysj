<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\data\Pagination;

use app\models\Category;
use app\models\SeaShellResult;

/*
* [分类] 入口 文件 by echo add 2016-06-13
* 所有的方法 都未做数据安全校验/权限限制 /日志记录 后续待完善
*
*/
class CategoryController extends Controller{

    /**
    * [查询接口一：店铺已有分类]
    *
    */
    function actionXXX(){

    }

    /**
    * [查询接口二：获取子分类] by echo add 2016-06-13
    *
    */
    function actionGet(){
        $shopId = Yii::$app->request->get('shopId');//店铺ID 用来校验 店铺是否真实有效
        $parentId = Yii::$app->request->get('parentId');//父ID：查最大类别 则传0;
        if( empty($shopId) ){
            $message = "请传入参数 [店铺ID、父分类ID]";
            return SeaShellResult::errorInfo($message);
        }
        if( empty($parentId) ){//不传则默认是请求最大的分类
            $parentId=0;
        }
        $categoryModel = new Category();
        $delResult = $categoryModel->getCategory($shopId,$parentId);
        return $delResult;

    }

}

?>