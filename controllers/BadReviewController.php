<?php
namespace app\controllers;
use Yii;
use yii\web\Controller;
use app\models\Product;
use app\models\SeaShellResult;
use yii\data\Pagination;
use app\models\Store;
use app\models\BadReview;
use app\models\BadReviewMonitor;
use app\service\SmsService;
/*
* add by echo 2016-06-04
*/

Class BadReviewController extends BaseController{
    public $layout = false;

    public $maxmonitor = 10;

    public function __construct($id, $module)
    {
        parent::__construct($id, $module);

        $this->initProductManagement();
    }

    public function actionList(){
        // var_dump($this->_request->get());die;
        $shopId = $this->_request->get('shopId');
        $orderBy = $this->_request->get('orderBy');
        $search_asin = $this->_request->get('asin', '');

        if(empty($shopId)){
            $missing = "shopId Miss";
            return SeaShellResult::error($missing);
        }

        $view = "list";
        $pageNo = $this->_request->get('page_no');
        $pageSize = $pageNo ?: 10;

        $query = BadReviewMonitor::find()
            ->leftJoin('sea_bad_review', 'sea_bad_review.monitor_id = sea_bad_review_monitor.id')
            ->where(['sea_bad_review_monitor.user_id'=>$this->_userId]);

        if ($orderBy) {
            if (stripos($orderBy, 'star') !== false) {
                $query->orderBy('sea_bad_review.' . $orderBy.',sea_bad_review.review_date desc');
            } else {
                $query->orderBy('sea_bad_review.' . $orderBy);
            }
        } else {
            $query->orderBy('sea_bad_review.review_date desc,sea_bad_review.id desc');
        }
        /*$query = BadReviewMonitor::find()
            ->with([
                'reviews' => function ($q) use ($orderBy) {
                    if ($orderBy) {
                        $q->orderBy($orderBy);
                    } else {
                        $q->orderBy('review_date desc,id desc');
                    }
                }
            ]);*/
        if ($search_asin) {
            $query->andWhere(['sea_bad_review_monitor.asin' => $search_asin]);
        }
        $num = $query->count();
        $pages = new Pagination([
            'defaultPageSize' => $pageSize,
            'totalCount' => $num,
            'pageSize' => $pageSize,
        ]);

        $query->select(['sea_bad_review.*', 'sea_bad_review_monitor.asin as m_asin', 'sea_bad_review_monitor.id as m_id']);
        
        $reviews = $query->offset($pages->offset)
                    ->limit($pages->limit)/*->orderBy('sea_bad_review_monitor.id desc')*/->asArray()->all();
        $read = BadReviewMonitor::readReview($this->_userId);
        if($read){
            $this->_BRcount = 0;
        }
        // ====================
        $data['reviews']=$reviews;
        $data['pages']=$pages;
        $data['totalCount'] = $num;
        $data['sort'] = empty($orderBy) ? "" : explode(" ",$orderBy)[1];
        $data['search_asin'] = empty($search_asin) ? "" : $search_asin;
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
        //$asins = "B01EA8EENG\nB00WGDEON8";
        $asin_arr = explode("\n", trim($asins));

        if( !$this->actionCheckmaxnumber($asin_arr) ){
            return SeaShellResult::error('超出最大可监控数: ' .$this->maxmonitor. "个");
        }

        if( count($asin_arr) ){
            foreach( $asin_arr as $asin ){
                BadReviewMonitor::addAsin($asin, $userId);

                // $badService = new \app\service\BadReviewCollectService();
                // $data = $badService->getNewestReview($asin);
                // if($data){
                //     BadReview::setFields($data)->insert();
                // }
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

        $spyTable = BadReviewMonitor::tableName();

        $query = (new \yii\db\Query())
            ->from($spyTable)
            ->where(['user_id' => $userId]);
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
    *Cancel asin
    */
    function actionDeleteAsin(){
        $userId=$this->_userId;
        $id = Yii::$app->request->post('id');

        $model = new BadReviewMonitor();
        $res = $model->delMonitor($id,$userId);

        if(!$res){
            return SeaShellResult::error('删除失败，请联系管理员');
        }
        return SeaShellResult::success($id);
    }


}


?>