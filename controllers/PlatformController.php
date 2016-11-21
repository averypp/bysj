<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Platform;
use app\models\SeaShellResult;


/*
* 平台/站点 的入口文件 by echo add 2016-06-12
* 所有的方法 都未做数据安全校验/权限限制 /日志记录 后续待完善
*
*/
class PlatformController extends Controller{

    /*
    * [查询接口：一] add by echo 2016-06-12 （已测试通过）
    * 获取所有平台列表数据（pid：0）
    * 返回json格式的SeaShellResult
    */
    function actionShowPlatformList(){
        $pid=0;
        $platforms = Platform::find()
        ->where(['is_deleted' => 'N','pid' => $pid])
        ->all();
        return SeaShellResult::arrayToJson($platforms);
    }

    /*
    * [查询接口：二] add by echo 2016-06-12 （已测试通过）
    * 获取某一平台下的站点数据（如要查pid为0的数据 @actionShowPlatformList）
    * 返回json格式的SeaShellResult
    */
    function actionShowSiteList(){
        $pid = Yii::$app->request->get('pid');//当前平台的ID
        if(empty($pid)){// 这里不允许pid为0
            $message = "请传入参数pid";
            return SeaShellResult::errorInfo($message);
        }
        $platforms = Platform::find()
            ->where(['is_deleted' => 'N','pid' => $pid])
            ->all();
        return SeaShellResult::arrayToJson($platforms);
    }

    /*
    * [修改接口：三] add by echo 2016-06-12 （已测试通过）
    * 修改 平台名称 (可优化：明知只会有一个结果。还取全表数据 只是为了方便修改操作？)
    */
    function actionEditPlatform(){
        $id = Yii::$app->request->get('id');//当前平台的ID(自增ID)
        $platformName = Yii::$app->request->get('platform_name');//当前平台的名称
        if(empty($id) || empty($platformName)){
            $message = "请传入 id 和 平台名称";
            return SeaShellResult::errorInfo($message);
        }
        $platforms = Platform::find()
        ->where(['is_deleted' => 'N','id' => $id,'pid' => 0])
        ->all();
        foreach($platforms as $platform){
            $platform->platform_name = $platformName;
            print_r($platform);
            $platform->save();
        }

        return SeaShellResult::arrayToJson($platform);

    }

    /*
    * [修改接口：四] add by echo 2016-06-12 （已测试通过）
    * 修改 某平台下的站点信息
    */
    function actionEditSite(){
        $id = Yii::$app->request->get('id');//当前站点的ID
        $pid = Yii::$app->request->get('pid');//当前站点的PID
        $platformName = Yii::$app->request->get('platform_name');//当前平台的名称
        if(empty($id) || empty($platformName) || empty($pid) ){
            $message = "请传入 ID 和PID 和 平台名称";
            return SeaShellResult::errorInfo($message);
        }
        $platforms = Platform::find()
        ->where(['is_deleted' => 'N','id' => $id,'pid' => $pid])
        ->all();
        foreach($platforms as $platform){
            $platform->platform_name = $platformName;
            print_r($platform);
            $platform->save();
        }
        return SeaShellResult::arrayToJson($platform);
    }

    /*
    * [新增接口：五] add by echo 2016-06-12 (已测试通过)
    * 新增 平台接口
    */
    function actionAddPlatform(){
        $platformName = Yii::$app->request->get('platform_name');//当前平台的名称
        $pid = 0;//当前站点的PID
        if( empty($platformName) ){
            $message = "请传入 [平台名称]";
            return SeaShellResult::errorInfo($message);
        }
        $platform = new Platform();
        $platform->pid = $pid;
        $platform->platform_name = $platformName;
        $platform->gmt_create = date("Y-m-d H:i:s");
        $platform->save();
        return SeaShellResult::arrayToJson($platform);
    }

    /*
    * [新增接口：六] add by echo 2016-06-12 (已测试通过)
    * 新增 某平台下的站点信息 接口
    */
    function actionAddSite(){
        $pid = Yii::$app->request->get('pid');//当前站点的平台ID
        $platformName = Yii::$app->request->get('platform_name');//当前平台的名称
        if( empty($pid) || empty($platformName) ){
            $message = "请传入 [平台ID 和平台名称]";
            return SeaShellResult::errorInfo($message);
        }
        $platform = new Platform();
        $platform->pid = $pid;
        $platform->platform_name = $platformName;
        $platform->gmt_create = date("Y-m-d H:i:s");

        $platform->save();
        return SeaShellResult::arrayToJson($platform);
    }


}



?>