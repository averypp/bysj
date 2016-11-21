<?php
namespace app\controllers;
use Yii;
use yii\web\Controller;
use app\models\Product;
use app\models\GoodsSoldInfo;
use app\models\SeaShellResult;
use yii\data\Pagination;


/*
* add by echo 2016-06-04
*/

Class DemoController extends Controller{

    

    /*
	* 分页查询方法
	*
	*/
	function actionQuery(){
	    $view = "selling";//什么视图
	    $pageSize = 10;
        $productTable = Product::tableName();
        $productSaleTable = GoodsSoldInfo::tableName();

        //前端查询参数
        $goodsName = Yii::$app->request->get('goods_name');   // 标题
        $qtyStatus = Yii::$app->request->get('qty_status');   // 库存状态：1：有库存0：没有库存 不传则查询全部
        $shopId = Yii::$app->request->get('shop_id');
        if(empty($shopId)){
            $missing = "shopId Miss";
            return SeaShellResult::errorInfo($missing);

        }


	    $query = (new \yii\db\Query())
            ->select(['id', 'goods_name'])
            ->from($productTable)
            //->leftJoin($productSaleTable,$productSaleTable.".goods_info_id = ".$productTable.".id ")
            ->where(['is_deleted' => 'N','shop_id' => $shopId]);

         if ($goodsName) {
             $query->andWhere(['like', 'goods_name', $goodsName]);
         }

        $page = new Pagination([
            'defaultPageSize' => $pageSize,
            'totalCount' => $query->count(),
        ]);
        $products = $query->orderBy($productTable.'.id')
                    ->offset($page->offset)
                    ->limit($page->limit)
                    ->all();

        $goodsInfoIds = array();
        foreach ($products as $product){
            array_push($goodsInfoIds, $product['id']);
        }

        //取出产品ID集合 获取售卖信息
        $queryGoodsSaleInfos = (new \yii\db\Query())
              ->from($productSaleTable)
              ->where(['is_deleted' => 'N'])
              ->andWhere(['in','goods_info_id',$goodsInfoIds])->all();
              //->createCommand();
        //echo $queryGoodsSaleInfo->sql;//打印 SQL 语句
        //print_r($queryGoodsSaleInfo->params);//打印被绑定的参数
        foreach ($products as $key=>$product){
            $products[$key]['goods_sale_info'] = array();
            foreach($queryGoodsSaleInfos as $queryGoodsSaleInfo){
                if($product['id'] == $queryGoodsSaleInfo['goods_info_id']){
                     array_push($products[$key]['goods_sale_info'],$queryGoodsSaleInfo);
                }
            }
        }

        print_r(SeaShellResult::arrayToJson($products));die();

        return  $this->render($view, [
                    'products' => $products,
                    'page' => $page,
                ]);

	}

	public function actionDemo(){
        $view = "demo";//什么视图
	    echo "haha";
        die();
        


        // 用用户输入来填充模型的特性
        $demo->attributes = \Yii::$app->request->get('ContactForm');

        if ($model->validate()) {
            // 若所有输入都是有效的
        } else {
            // 有效性验证失败：$errors 属性就是存储错误信息的数组
            $errors = $model->errors;
        }

        //批量更新updateAll($param1,$param2)   $param1为要更新的字段 $param2为 更新的条件
        //update customer set status = 1 where status = 2 and uid = 1;
        //Customer::updateAll(['status' => 1], ['status'=> '2','uid'=>'1']);


        //批量插入
        //Yii::$app->db->createCommand()->batchInsert(UserModel::tableName(), ['user_id','username'], [
        //    ['1','test1'],
        //    ['2','test2'],
        //    ['3','test3'],
        //])->execute();



	    return  $this->render($view, [
                            'products' => "",
                        ]);
	}



}


?>