<?php
namespace app\controllers;
use Yii;
use yii\web\Controller;
use app\models\Product;
use app\models\SeaShellResult;
use yii\data\Pagination;
use app\models\Monitor;
use app\models\FollowsellerDetail;
use app\models\Store;
/*
* add by echo 2016-06-04
*/

Class MonitorController extends BaseController{
    public $layout = false;
    private $_status = ['current' => "current", 'old' => "old"];

    public $maxmonitor = 5;

    public function __construct($id, $module)
    {
        parent::__construct($id, $module);

        $this->initProductManagement();
    }

    public function actionList(){
        $shopId = Yii::$app->request->get('shopId');
        if(empty($shopId)){
            $missing = "shopId Miss";
            return SeaShellResult::error($missing);
        }

        $session = Yii::$app->session;
        $user_id=$session->get("user_id");
        $view = "list";
        $pageSize=30;
        $monitorTable = Monitor::tableName();
        $query = (new \yii\db\Query())
            ->from($monitorTable)
            ->where(['user_id' => $user_id, "is_deleted" => "N"]);
        $num = $query->count();
        
        $pages = new Pagination([
            'defaultPageSize' => $pageSize,
            'totalCount' => $num,
        ]);

        $products = $query->orderBy($monitorTable.'.is_monitor desc')
                    ->offset($pages->offset)
                    ->limit($pages->limit)
                    ->all();
        
        
        if(count($products)){
            $exclude_seller = '';
            foreach($products as &$product){
                //print_r($product);
                $low_price = $seller_count = $fba_count = 0;
                $exclude_seller = $product['exclude_seller'];
                if($product['is_monitor'] && strlen($exclude_seller)){
                    $seller_count = FollowsellerDetail::find()->where(['monitor_id' => $product['id'], 'follow_sell_end_at' => 0, 'is_deleted' => 'N'])->andwhere(['not in', 'seller_id', explode(',', $exclude_seller)])->count();
                    $fba_count = FollowsellerDetail::find()->where(['monitor_id' => $product['id'], 'isFBA' => 1, 'follow_sell_end_at' => 0, 'is_deleted' => 'N'])->andwhere(['not in', 'seller_id', explode(',', $exclude_seller)])->count();
                    $low_price = FollowsellerDetail::find()->select(['(price+shopping_fee) as low_price'])->where(['monitor_id' => $product['id'], 'is_deleted' => 'N'])->andwhere(['not in', 'seller_id', explode(',', $exclude_seller)])->orderBy('low_price asc')->asArray()->one();

                    $product['seller_count'] = $seller_count?:0;
                    $product['fba_count'] = $fba_count?:0;
                    $product['low_price'] = count($low_price)?$low_price['low_price']:0;
                }
            }
        }

        $data['products']=$products;
        $data['pages']=$pages;
        $data['totalCount'] = $num;
        $data['requestUri'] = $this->getRequestUri('page_no');
        $data['shopInfo'] = $this->_shopInfo;
        $data['BRcount'] = $this->_BRcount;
        return  $this->render($view, $data);
    }

    /**
    *Add asin 
    */
    function actionAddAsin(){
        $userId=$this->_userId;
        $asins = Yii::$app->request->post('asins');
        //$asins = "B01EA8EENG,B00WGDEON8";
        $asin_arr = explode(",", trim($asins));

        // if( !$this->actionCheckmaxnumber($asin_arr) ){
        //     return SeaShellResult::error('超出最大可监控数 ' .$this->maxmonitor. "个");
        // }

        if( count($asin_arr) ){
            $model = new Monitor();
            foreach( $asin_arr as $asin ){
                $model->addAsin($asin, $userId);
            }
            return SeaShellResult::success('success');
        }

        return SeaShellResult::success('1');
    }

    /**
    *Check monitor asin number
    */
    function actionCheckmaxnumber($asin_arr=[]){
        $userId=$this->_userId;

        $spyTable = Monitor::tableName();

        $query = (new \yii\db\Query())
            ->from($spyTable)
            ->where(['is_monitor' => 1, 'user_id' => $userId]);
        if(count($asin_arr)){
            $query->andWhere(['not in', 'asin', $asin_arr]);
        }
        
        $count = $query->count();
        if( $count+count($asin_arr) > $this->maxmonitor ){
            return false;
        }
        return true;
    }

    /**
    *Modify excluded seller
    */
    function actionEditMonitor(){
        $userId=$this->_userId;
        $id = Yii::$app->request->get('id');
        //$asin = Yii::$app->request->post('asin');
        $seller = Yii::$app->request->get('seller');

        $model = new Monitor();
        $res = $model->modifyExcludeseller($id, $seller,$userId);

        if(!$res){
            return SeaShellResult::error('修改失败，请联系管理员');
        }
        return SeaShellResult::success('1');
    }

    /**
    *Cancel asin to monitor
    */
    function actionCancelMonitor(){
        $userId=$this->_userId;
        $id = Yii::$app->request->post('id');

        $model = new Monitor();
        $res = $model->cancelMonitor($id,$userId);

        if(!$res){
            return SeaShellResult::error('取消失败，请联系管理员');
        }
        return SeaShellResult::success('1');
    }


    /**
    *Open asin to monitor
    */
    function actionOpenMonitor(){
        $userId=$this->_userId;
        $id = Yii::$app->request->post('id');

        if( !$this->actionCheckmaxnumber() ){
            return SeaShellResult::error('超出最大可监控数 ' .$this->maxmonitor. "个");
        }

        $model = new Monitor();
        $res = $model->openMonitor($id,$userId);

        if(!$res){
            return SeaShellResult::error('开启失败，请联系管理员');
        }
        return SeaShellResult::success('1');
    }

    /**
    *del asin to monitor
    */
    function actionDelMonitor(){
        $userId=$this->_userId;
        $id = Yii::$app->request->post('id');

        $model = new Monitor();
        $res = $model->delMonitor($id,$userId);

        if(!$res){
            return SeaShellResult::error('删除失败，请联系管理员');
        }
        return SeaShellResult::success($id);
    }


    public function actionDetail($status = 'current')
    {
        $monitorId = $this->_request->get('id');
        $orderBy = $this->_request->get('orderBy');
        $pageSize = $this->_request->get('page_no', 30);
        $flag = $status == 'old' ? true : false;

        $lists = FollowsellerDetail::getDetailList($monitorId, $this->_userId, $pageSize, $flag, $orderBy);
        if (!$lists) {
            return $this->redirect('?r=monitor/list&shopId=' . $this->_shopId);
        }

        $lists['count'] = $this->detailCount($monitorId);
        $lists['shopInfo'] = $this->_shopInfo;
        $lists['sort'] = empty($orderBy) ? "" : explode(" ",$orderBy)[1];
        $lists['status'] = $status;
        $lists['monitorId'] = $monitorId;
        $lists['requestUri'] = $this->getRequestUri('page_no');
        $lists['BRcount'] = $this->_BRcount;
        // print_r('<pre>');var_dump($lists);die('</pre>');
        return $this->render('detail', $lists);
    }

    private function detailCount($monitorId)
    {
        $monitor = Monitor::findOne(['id' => $monitorId, 'is_deleted' => 'N']);
        

        $query = FollowsellerDetail::find()
            ->where(['monitor_id' => $monitorId]);
        if(strlen($monitor->exclude_seller)){
            $exclude_seller = explode(",", $monitor->exclude_seller);
            $query->andWhere(['not in', 'seller_id', $exclude_seller]);
        }
        $select = [
            "SUM(CASE WHEN follow_sell_end_at = 0 THEN 1 ELSE 0 END) AS new",
            "SUM(CASE WHEN follow_sell_end_at > 0  THEN 1 ELSE 0 END) AS old",
        ];

        return $query->select($select)->asArray()->one();
    }

}


?>
