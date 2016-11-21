<?php
namespace app\controllers;
use Yii;
use yii\web\Controller;
use app\models\Product;
use app\models\GoodsSoldInfo;
use app\models\GoodsInfo;
use yii\data\Pagination;
use app\models\SeaShellResult;
use app\models\AmazonFeedValues;
use app\models\AmazonFeedTplData;
use app\models\AmazonTemplate;
use app\models\AmazonBtg;
use app\models\AmazonFeeds;
use yii\helpers\Json;
use app\models\Store;
use app\models\UploadForm;
use yii\web\UploadedFile;

/*
* 产（商）品管理 入口
* add by echo 2016-06-03
*/

Class ProductController extends BaseController
{
    public $layout=false; //重写属性，默认是加载layouts\main.php   这里不加载
    public $enableCsrfValidation = false;

    public function __construct($id, $module)
    {
        parent::__construct($id, $module);
        $this->initProductManagement();
    }

    /*
	* 分页查询方法
	*
	*/
	public function actionQuery(){
	    $view = "selling";//什么视图
	    $pageSize = 10;
        $productTable = Product::tableName();
        $productSaleTable = GoodsSoldInfo::tableName();

        //前端查询参数
        $goodsName = Yii::$app->request->get('goodsName');   // 标题
        $qtyStatus = Yii::$app->request->get('qtyStatus');   // 库存状态：1：有库存0：没有库存 不传则查询全部
        $shopId = Yii::$app->request->get('shopId');
        if(empty($shopId)){
            $missing = "shopId Miss";
            return SeaShellResult::error($missing);
        }
	    $query = (new \yii\db\Query())
            ->select(['id', 'item_name'])
            ->from($productTable)
            ->where(['is_deleted' => 'N','shop_id' => $shopId,'pub_status' => 4]);

         if ($goodsName) {
             $query->andWhere(['like', 'item_name', $goodsName]);
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
        foreach ($products as $key=>$product){
            $products[$key]['goods_sale_info'] = array();
            foreach($queryGoodsSaleInfos as $queryGoodsSaleInfo){
                if($product['id'] == $queryGoodsSaleInfo['goods_info_id']){
                     array_push($products[$key]['goods_sale_info'],$queryGoodsSaleInfo);
                }
            }
        }
        return  $this->render($view, [
                    'products' => $products,
                    'page' => $page,
                    'shopInfo' => $this->_shopInfo,
                    'BRcount' => $this->_BRcount
                ]);

	}
	//新建产品页面,保存草稿
    function actionCreateProduct(){
    	if(!$_POST){
            $shopId = Yii::$app->request->get('shopId');
            $site_id = Store::getSiteIdByShopId($shopId);
            $view = "create_product";
            $amazonBtg_model = new AmazonBtg();
            $catgory = $amazonBtg_model->getCategory($site_id ,$parent_id =0 ,null);
            return  $this->render($view, [
                        'catgory' => $catgory,
                        'shopInfo' => $this->_shopInfo,
                        'BRcount' => $this->_BRcount
            ]);
    	} else {
    		$model = new GoodsInfo();
    		$product_data = Yii::$app->request->post('product');
            $shopId = Yii::$app->request->post('shopId');
            $product_data = json_decode($product_data,true);
            $model->recordGoods($product_data, $shopId);
            return SeaShellResult::success('1');
        }
    }
    public function msgReport($msg, $status = false){
        $errorResponse = array("msg" => $msg,"status" =>$status);
        return $errorResponse;
    }
    function checkProductInfo($product_data){
        if(!$product_data['Title']){
            return $this->msgReport('请填写标题');
        }
        if(!$product_data['ParentSKU']){
            return $this->msgReport('请填写ParentSKU');
        }
        if(!$product_data['Category']['ID']){
            return $this->msgReport('请选择分类');
        }
        if(!trim($product_data['Description'])){
            return $this->msgReport('请填写产品描述');
        }
        if(!$product_data['Brand']){
            return $this->msgReport('请填写Brand');
        }
        if(!$product_data['Manufacture']){
            return $this->msgReport('请填写商品生产商');
        }
        if(!$product_data['ProductIdType']){
            return $this->msgReport('请选择商品编码类型');
        }
        if($product_data['VariationTheme'] == ''){
            if(!$product_data['UPC']){
                return $this->msgReport('请填商品编码');
            }
            if(!$product_data['StartPrice'] || !is_numeric($product_data['StartPrice'])){
                return $this->msgReport('请填写价格');
            }
            if(!$product_data['Quantity'] || !is_numeric($product_data['Quantity'])){
                return $this->msgReport('请填写库存');
            }

        } else {
            foreach ($product_data['ProductSKUs'] as $key => $value) {
                if( !($value["UPC"] && is_numeric($value['Price']) && $value['Price'] && is_numeric($value['Stock']) && $value['Stock'] && $value['SKU']) ){
                    return $this->msgReport('商品销售信息错误或不完整');
                }
            }

        }
        if(empty($product_data['PictureURLs'])){
            return $this->msgReport('请上传父产品图片');
        }
        $keyword_count = $bullet_count = 5;
        foreach ($product_data['KeyWords'] as $value) {
            if(!$value){
                $keyword_count--;
            }
        }
        if($keyword_count < 5){
            return $this->msgReport('产品关键词未填写完整');
        }
        foreach ($product_data['BulletPoints'] as $value) {
            if(!$value){
                $bullet_count--;
            }
        }
        if($bullet_count < 1){
            return $this->msgReport('bullet point不需要全部填写完整,可根据需求填写!');
        }
        if( !$product_data['ProductSpecifics'] || !is_array($product_data['ProductSpecifics']) ){
            return $this->msgReport('请填写商品参数信息');
        } else {
            foreach ($product_data['ProductSpecifics'] as $key => $value) {
                if(!$value['Value']){
                    return $this->msgReport('请填写商品参数信息');
                }
            }

        }
        return $this->msgReport('success', true);

    }
    //编辑页面,保存到草稿/待发布通过,pub_status判断
    function actionEditProduct(){
        if(!$_POST){
            $view = "edit_product";
            $goodId = Yii::$app->request->get('goodId');
            $shopId = Yii::$app->request->get('shopId');
            $site_id = Store::getSiteIdByShopId($shopId);
            $amazonBtg_model = new AmazonBtg();
            $catgory = $amazonBtg_model->getCategory($site_id ,$parent_id =0, null);
            $model = new GoodsInfo();
            $goods_info = $model->getProductInfoById($goodId, $shopId);
            //有变体情况下的父商品图片
            $goods_parent_img = $model->getParentImgById($goodId, $shopId);
            $productType = $this->get_producttype_update($goods_info['category_id'], $goods_info['tpl_id']);
            $variationData = $this->Get_specifics_update($goods_info['tpl_id'], $site_id, $goods_info['feed_product_type']);
            return  $this->render($view, [
                        'catgory' => $catgory,
                        'goods_info'=>$goods_info,
                        'shopInfo' => $this->_shopInfo,
                        'productType' => $productType,
                        'variation' => $variationData,
                        'goods_parent_img' => $goods_parent_img
                    ]);
        } else {
            $session = Yii::$app->session;
            $user_id = $session->get("user_id");
            $model = new GoodsInfo();
            $product_data = Yii::$app->request->post('product');
            $shopId = Yii::$app->request->post('shopId');
            $goodsId = Yii::$app->request->post('product_id');
            $pub_status = Yii::$app->request->post('pub_status');
            $product_data = json_decode($product_data, true);
            if($pub_status == 1){
                $check_return = $this->checkProductInfo($product_data);
                if(!$check_return['status']){
                    return SeaShellResult::error($check_return['msg']);
                }
            }
            $model->recordGoods($product_data, $shopId, $pub_status, $goodsId, true);
            //保存到待发布的时候，才生成提交数据
            if($pub_status == 1){
                if(!$this->actionCreateTplData($goodsId)){
                    return SeaShellResult::error('保存上传数据错误');
                }
            }
            return SeaShellResult::success('1');
        }
    }

    //新建产品页面,保存到带发布
    function actionCreateProductToPub(){
        $session = Yii::$app->session;
        $user_id=$session->get("user_id");
        $model = new GoodsInfo();
        $product_data = Yii::$app->request->post('product');
        $shopId = Yii::$app->request->post('shopId');
        $pub_status = Yii::$app->request->post('pub_status');
        $product_data = json_decode($product_data, true);
        $check_return = $this->checkProductInfo($product_data);
        if(!$check_return['status']){
            return SeaShellResult::error($check_return['msg']);
        }
        $goodsId = $model->recordGoods($product_data, $shopId, $pub_status);
        if($goodsId){
            if(!$this->actionCreateTplData($goodsId)){
                return SeaShellResult ::error('保存上传数据错误');
            }
            return SeaShellResult ::success('1');
        } else {
            return SeaShellResult ::error('');
        }
    }

    //生成模板数据并存储到feed表
    function actionCreateTplData($goods_id){
        $goodsinfo_model = new GoodsInfo();
        $amazonTemplate_model = new AmazonTemplate();
        $goodsInfo = GoodsInfo::find()
            ->with('skus', 'params')->where(['id' => $goods_id])->asArray()->one();
        if (!$goodsInfo) {
            return false;
        }
        $tpl_id = $goodsInfo['tpl_id'];
        //field label键值对
        $kvArray = AmazonFeedTplData::getFieldLableArray($tpl_id);
        //模板数据去循环
        $tplInfo = $goodsinfo_model->getTplById($tpl_id);
        //模板名称和版本
        $tplName = $amazonTemplate_model->getTemplateById($tpl_id);
        $feed = new \app\assets\amazonAPI\classes\helper\WPLA_FeedDataBuilder();
        if($tplInfo && $tplName && $kvArray){
            $feedData = $feed->buildNewProductsFeedData($goodsInfo ,$tplInfo ,$tplName, $kvArray);
        } else {
            return false;
        }

        $customer = AmazonFeeds::findOne(['good_id' => $goods_id]);
        if($customer){
            $customer->gmt_modified = date('Y-m-d H:i:s');
            $customer->good_id = $goods_id;
            $customer->FeedType = '_POST_FLAT_FILE_LISTINGS_DATA_';
            $customer->data = $feedData;
            $customer->save();
        } else {
            $amazonFeeds_model = new AmazonFeeds();
            $amazonFeeds_model->date_created = date('Y-m-d H:i:s');
            $amazonFeeds_model->gmt_create = date('Y-m-d H:i:s');
            $amazonFeeds_model->gmt_modified = date('Y-m-d H:i:s');
            $amazonFeeds_model->good_id = $goods_id;
            $amazonFeeds_model->FeedType = '_POST_FLAT_FILE_LISTINGS_DATA_';
            $amazonFeeds_model->data = $feedData;
            $amazonFeeds_model->tpl_id = $tpl_id;
            $amazonFeeds_model->save();
        }
        return true;
    }

    function actionGetGoodInfo(){
        $good_id = Yii::$app->request->get('goodId');
        $shopId = Yii::$app->request->get('shopId');
        $model = new GoodsInfo();
        $goods_info = $model->getProductInfoById($good_id, $shopId);
        $product['Brand'] = $goods_info['brand_name'];
        $product['BrandSeller'] = $goods_info['is_brand'] == 'Y' ? 1 : 0;
        $product['BulkSell'] = false;
        $product['BulletPoints'] = unserialize($goods_info['bullet_points']);
        $product['Category'] = [
            'ID' => $goods_info['category_id'],
            'Name' => explode('>',$goods_info['category_name'])
        ];
        $product['CategoryRoot'] = '';
        $product['CategoryUID'] = '';
        $product['Condition'] = ['ID'=>'', 'Name'=>$goods_info['condition_type']];
        $product['Department'] = '';
        $product['Description'] = $goods_info['product_description'];
        $product['DispatchTimeMax'] = $goods_info['stocking_time'];
        $product['ItemType'] = $goods_info['item_type'];
        $product['KeyWords'] = explode(',', $goods_info['generic_keywords']);
        $product['MSRP'] = $goods_info['list_price'];
        $product['Manufacture'] = $goods_info['manufacturer'];
        $product['ParentSKU'] = $goods_info['parent_sku'];
        $imageArray = [];
        if($goods_info['skus'] && $goods_info['variation_theme']){
            foreach ($goods_info['skus'] as $key => $value) {
                foreach ($value['pic'] as $k => $v) {
                    if($v['goods_picture_type'] == 1){
                            $imageArray[] = $v['image_url'];
                    }
                }
            }
        } else {
            foreach ($goods_info['skus'] as $key => $value) {
                foreach ($value['pic'] as $k => $v) {
                    if($v['goods_picture_type'] == 0){
                            $imageArray[] = $v['image_url'];
                    }
                }
            }
        }
        $product['PictureURLs'] = $imageArray;
        $product['Brand'] = $goods_info['brand_name'];
        $product['ProductIdType'] = $goods_info['external_product_id_type'];
        $product['ProductType'] = $goods_info['feed_product_type'];
        if($goods_info['skus']){
            foreach ($goods_info['skus'] as $key => $value) {
                $picArray = [];
                foreach ($value['pic'] as $k => $v) {
                    if($v['goods_picture_type'] == 0 && $v['sku_id'] == $value['id']){
                        $picArray[] = $v['image_url'];
                    }
                }
                $VariationSpecifics = [];
                foreach ($value['specs'] as $k => $v) {
                    if($v['sku_id'] == $value['id']){
                        $VariationSpecifics[] = ['Image' => [], 'Name' => $v['field'], 'NameID' => '', 'Value' => $v['value'], 'ValueID' => ''];
                    }
                }

               $product['ProductSKUs'][$key] = [
                    'PictureURL' => $picArray,
                    'Price' => $value['standard_price'],
                    'SKU' => $value['item_sku'],
                    'Sale' => [
                        'SaleDateFrom' => $value['sale_from_date'], 'SaleDateTo' => $value['sale_end_date'], 'SalePrice' => $value['sale_price']
                    ],
                    'Stock' => $value['quantity'],
                    'UPC' => $value['external_product_id'],
                    'VariationSpecifics' => $VariationSpecifics
               ];
            }
        }


        if($goods_info['params']){
            foreach ($goods_info['params'] as $key => $value) {
                $product['ProductSpecifics'][] = [
                    'Name' => $value['field'], "NameID" =>'', 'Value' => $value['value'], 'ValueID' =>''
                ];
            }
        }
        if(!$goods_info['variation_theme']){
                $product['Sale'] = [
                    'SaleDateFrom'=>$goods_info['skus'][0]['sale_from_date'], 'SaleDateTo'=>$goods_info['skus'][0]['sale_end_date'], 'SalePrice'=>$goods_info['skus'][0]['sale_price']
                ];
                $product['Quantity'] = $goods_info['skus'][0]['quantity'];
                $product['StartPrice'] = $goods_info['skus'][0]['standard_price'];
                $product['UPC'] =  $goods_info['skus'][0]['external_product_id'];
        }
        $product['ShippingWeight'] = $goods_info['website_shipping_weight'];
        $product['SupplyLink'] = $goods_info['supply_link'];
        $product['Title'] = $goods_info['item_name'];
        $product['VariationTheme'] = $goods_info['variation_theme'];
        $product['WeightUnit'] = $goods_info['website_shipping_weight_unit_of_measure'];
        $Response = array("flag" => false,"product" =>$product,"specifics"=>[], 'status'=>1);
        return Json::encode($Response);

    }

     function actionUploadImage(){
        if($_FILES["Filedata"]["error"] > 0){
            return $this->returnJsonData(false,$_FILES["Filedata"]["error"]);
        }
        if ((($_FILES["Filedata"]["type"] == "image/gif")
        || ($_FILES["Filedata"]["type"] == "image/jpeg")
        || ($_FILES["Filedata"]["type"] == "image/jpg")
        || ($_FILES["Filedata"]["type"] == "image/png")
        || ($_FILES["Filedata"]["type"] == "image/pjpeg"))
        && ($_FILES["Filedata"]["size"] < 5000000))
        {
            if (file_exists("web/upload/" . $_FILES["Filedata"]["name"])){
            } else {
              move_uploaded_file($_FILES["Filedata"]["tmp_name"],
             dirname(__DIR__)."/web/upload/" . $_FILES["Filedata"]["name"]);
            }
        } else {
            return $this->returnJsonData(false,"Invalid Filedata");
        }
        $data['url'] = 'http://'.$_SERVER['SERVER_NAME']."/upload/" . $_FILES["Filedata"]["name"];
        return $this->returnJsonData(true,"upload pic success",$data);
     }

    //父级分类展示，及查找下一级分类
    function actionGet_category(){
      $amazonBtg_model = new AmazonBtg();
      $store_model = new Store();
      $parent_id = Yii::$app->request->get('parent_id');
      $shop_id = Yii::$app->request->get('shopId');
      $tpl_id = Yii::$app->request->get('tpl_id');
      $site_id =  $store_model->findSiteidByShopid($shop_id);//store表里没有数据，先用默认值
      $cat_data = $amazonBtg_model->getCategory($site_id, $parent_id, $tpl_id);
      if(!$cat_data){
        $Response = array("categories"=>'');
        return Json::encode($Response);

      }else{
        $Response = array("categories"=>$cat_data);
        return Json::encode($Response);
      }
    }


    //查询变体信息（actneed 的productType项目）
    function actionGet_producttype(){
        $AmazonFeedValues_model = new AmazonFeedValues();
        $amazonBtg_model = new AmazonBtg();
        $child_id = Yii::$app->request->get('child_id');
        $levelId = Yii::$app->request->get('levelId');
        $tpl_id = Yii::$app->request->get('tpl_id');
        $btg_data = $amazonBtg_model->getBtgData($child_id, $levelId, $tpl_id);
        $item_type = $btg_data['keyword'];
        $tpl_id = $btg_data['tpl_id'];
        $site_id = $btg_data['site_id'];
        $product_type = $AmazonFeedValues_model->getProducttype($tpl_id ,$site_id);
        if($product_type){
          return json_encode(["status" => true,"product_type" => $product_type,"item_type" => $item_type, "special_upc" => ["MfrPartNumber","ModelNumber"], 'other_value' => false, 'tpl_id' => $tpl_id, 'site_id' => $site_id]);
        } else {
          return json_encode(["status" => true,"msg" => "no data", 'tpl_id' => $tpl_id, 'site_id' => $site_id]);
        }
    }


    //for edit get producttype datas
    function Get_producttype_update($child_id, $tpl_id){
        $AmazonFeedValues_model = new AmazonFeedValues();
        $amazonBtg_model = new AmazonBtg();
        $btg_data = $amazonBtg_model->getBtgDataUpdate($child_id, $tpl_id);
        $item_type = $btg_data['keyword'];
        $tpl_id = $btg_data['tpl_id'];
        $site_id = $btg_data['site_id'];
        $product_type = $AmazonFeedValues_model->getProducttype($tpl_id ,$site_id);
        return $product_type;
    }

    //for edit get datas
    function Get_specifics_update($tpl_id, $site_id, $product_type){
        $isCreate = false;
        $params = $this->getSpecificsParams($tpl_id, $site_id, $product_type, $isCreate);
        return $params;
    }

    function splitColorSize($param){
        $outParam[] = $param;
        if(strpos($param, '-')){
            $outParam = explode('-', $param);
        } else {
            if(stripos($param, 'size') === 0 ){
                $pattern = '/^(size)[^c]{0,4}/i';
                preg_match($pattern, $param, $matches);
                if($param == $matches[0]){
                    //$outParam[] = $param;
                } else {
                    $outParam = [substr($param, 0, strlen($matches[0])), substr($param, strlen($matches[0]), -1)];
                }
            }
            if(stripos($param, 'color') === 0){
                $pattern = '/^(color)[^s]{0,4}/i';
                preg_match($pattern, $param, $matches);
                if($param == $matches[0]){
                    //$outParam[] = $param;
                } else {
                    $outParam = [substr($param, 0, strlen($matches[0])), substr($param, strlen($matches[0]))];
                }
            }

        }
        return $outParam;

    }

    //2016.10.26   ljp
    function actionGet_specifics(){

        $tpl_id = Yii::$app->request->post('tpl_id');
        $site_id = Yii::$app->request->post('site_id');
        $product_type = Yii::$app->request->post('product_type');
        $isCreate = true;
        $params = $this->getSpecificsParams($tpl_id, $site_id, $product_type, $isCreate);
        return $params;
    }

    function getSpecificsParams($tpl_id, $site_id, $product_type, $isCreate = true){
        $variationThemes = AmazonFeedValues::getVariationThemes($tpl_id ,$site_id ,$product_type);
        //所有参数
        $all_fields = AmazonFeedTplData::getAllFields($tpl_id, $site_id);
        $variations = $attrbutes = [];
        $variationThemes_default = ['Color', 'Size', 'Color-Size'];
        //$size_color = ['sizecolor', 'SizeColor', 'colorsize', 'ColorSize'];
        //根据有的信息 生产colorsize信息
        if( !empty($variationThemes) ){
            $variationThemes_data = $variationThemes;
            $attrbutes[0] = [];//占位
            foreach( $variationThemes_data as $variation){
                if( $variation ){
                    $variations[] = ['v_name' => $variation, 'relation' => $this->splitColorSize($variation)];
                }else{
                     $variations[] = ["v_name" => $variation, "relation" => ["Color", "Size"]];
                }
            }
            $sigleAttributList = [];
            //单变体模板属性组装 colo size type ......
            foreach($variations as $variation){
                if( count($variation['relation']) == 1 ){
                    $sigleAttributList[] = $variation['v_name'];
                    $field = '';
                    if( in_array($variation['v_name'], array_keys($all_fields)) ){
                        $field = $all_fields[ $variation['v_name'] ];
                    }
                    if( $variation['v_name'] == "Color" || $variation['v_name'] == "ColorName"){
                        $field=['color_map', 'color_name'];
                    }
                    if( $variation['v_name'] == "SizeName" || $variation['v_name'] == "Size"){
                        $field=['size_map', 'size_name'];
                    }
                    $v_values = AmazonFeedValues::getFieldValues($tpl_id, $site_id, $field, [$product_type, '']);
                    $attrbutes[] = ["ShowType"=>"CheckBox","name"=>$variation['v_name'],"required"=>false,"sku"=>true,"values"=>$v_values];
                }
            }
            //多变体模板属性替换，比如sizename-colorname 前有一个color属性，则把colorname换成color
            foreach ($variations as &$variation) {
                if(count($variation['relation']) > 1){
                    foreach ($variation['relation'] as &$value) {
                        foreach ($sigleAttributList as $sigleAttribut) {
                            if(stripos($value, $sigleAttribut) !== false){
                                $value = $sigleAttribut;
                            }
                        }
                    }
                }
            }
            $attrbutes[0] = ["ShowType" => "List", "name" => "VariationTheme", "required" => true, "sku" => false, "values" => $variations];
        }else{
            //设置默认的colorsize
            $variationThemes_data = $variationThemes_default;
            foreach( $variationThemes_data as $variation){
                $variations[] = ['v_name' => $variation, 'relation' => explode('-', $variation)];
            }
            $attrbutes[] = ["ShowType" => "List", "name" => "VariationTheme", "required" => true, "sku" => false, "values" => $variations];

            $v_values = AmazonFeedValues::getFieldValues($tpl_id, $site_id, ['color_map', 'color_name']);
            $attrbutes[] = ["ShowType"=>"CheckBox","name"=>'Color',"required"=>false,"sku"=>true,"values"=>$v_values];

            $v_values = AmazonFeedValues::getFieldValues($tpl_id, $site_id, ['size_map', 'size_name']);
            $attrbutes[] = ["ShowType"=>"CheckBox","name"=>'Size',"required"=>false,"sku"=>true,"values"=>$v_values];
        }
        //变体模板属性与必填字段相似，用变体模板属性展示
        $required_attr = AmazonFeedTplData::getRequiredField($tpl_id, $site_id, $product_type);
        //var_dump($required_attr);die;
        if($required_attr){
            foreach($required_attr as $label => &$field_name){
                foreach($attrbutes as $key => &$attrbute){
                    if( $this->filterParams($attrbute['name'], $label) ){
                        $attrbute['required'] = true;
                        unset($required_attr[$label]);
                    }
                }
            }
        }
        //过滤后的必填字段组装
        if($required_attr){
            foreach($required_attr as $label => &$field_name){
                $values = AmazonFeedValues::getFieldValues($tpl_id, $site_id, $field_name, $product_type);
                if(empty($values)){
                    $attrbutes[] = ["ShowType"=>"String","name"=>$label,"required"=>true,"sku"=>false,"values"=>''];
                }else{
                    $attrbutes[] = ["ShowType"=>"List","name"=>$label,"required"=>true,"sku"=>false,"values"=>$values];
                }
            }
        }

        if($isCreate){
            if(count($attrbutes) == 0){
                return json_encode(["status"=>false,"msg"=>"no data"]);
            }
            $response = array("content" => $attrbutes, "success" => true, "specifics" => []);
            return Json::encode($response);
        } else {
            if(count($attrbutes) == 0){
                return [];
            }
            return $attrbutes;
        }
    }

    function filterParams($attribute, $requireLabel){
        $ret = 0;
        if(stripos($attribute, $requireLabel) !== false){
            $ret =1;
        }
        return $ret;
    }
    function actionGet_specificsBak(){

        $tpl_id = Yii::$app->request->post('tpl_id');
        $site_id = Yii::$app->request->post('site_id');
        $product_type = Yii::$app->request->post('product_type');
        $variationThemes = AmazonFeedValues::getVariationThemes($tpl_id ,$site_id ,$product_type);
        $all_fields = AmazonFeedTplData::getAllFields($tpl_id, $site_id);
        $variations = $attrbutes = [];
        $variationThemes_default = ['Color', 'Size', 'Color-Size'];
        $size_color = ['sizecolor', 'SizeColor', 'colorsize', 'ColorSize'];

        if( !empty($variationThemes) ){
            $variationThemes_data = $variationThemes;
            $attrbutes[0] = [];
            foreach( $variationThemes_data as $variation){
                if( !in_array($variation, $size_color) ){
                    $variations[] = ['v_name' => $variation, 'relation' => explode('-', $variation)];
                }else{
                     $variations[] = ["v_name" => $variation, "relation" => ["Color", "Size"]];
                }
            }
            foreach($variations as $variation){
                if( count($variation['relation']) == 1 ){
                    $field = '';
                    if( in_array($variation['v_name'], array_keys($all_fields)) ){
                        $field = $all_fields[ $variation['v_name'] ];
                    }
                    if( $variation['v_name'] == "Color" || $variation['v_name'] == "ColorName"){
                        // $variation['v_name'] = 'Color';
                        $field=['color_map', 'color_name'];
                    }
                    if( $variation['v_name'] == "SizeName" || $variation['v_name'] == "Size"){
                        // $variation['v_name'] = 'Size';
                        $field=['size_map', 'size_name'];
                    }
                    $v_values = AmazonFeedValues::getFieldValues($tpl_id, $site_id, $field, [$product_type, '']);
                    $attrbutes[] = ["ShowType"=>"CheckBox","name"=>$variation['v_name'],"required"=>false,"sku"=>true,"values"=>$v_values];
                }
            }
            $attrbutes[0] = ["ShowType" => "List", "name" => "VariationTheme", "required" => true, "sku" => false, "values" => $variations];

        }else{
            $variationThemes_data = $variationThemes_default;

            foreach( $variationThemes_data as $variation){
                $variations[] = ['v_name' => $variation, 'relation' => explode('-', $variation)];
            }
            $attrbutes[] = ["ShowType" => "List", "name" => "VariationTheme", "required" => true, "sku" => false, "values" => $variations];

            $v_values = AmazonFeedValues::getFieldValues($tpl_id, $site_id, ['color_map', 'color_name']);
            $attrbutes[] = ["ShowType"=>"CheckBox","name"=>'Color',"required"=>false,"sku"=>true,"values"=>$v_values];

            $v_values = AmazonFeedValues::getFieldValues($tpl_id, $site_id, ['size_map', 'size_name']);
            $attrbutes[] = ["ShowType"=>"CheckBox","name"=>'Size',"required"=>false,"sku"=>true,"values"=>$v_values];
        }
        $required_attr = AmazonFeedTplData::getRequiredField($tpl_id, $site_id, $product_type);
        if($required_attr){
            foreach($required_attr as $label => &$field_name){
                foreach($attrbutes as $key => &$attrbute){
                    if( $attrbute['name'] == $label ){
                        $attrbute['required'] = true;
                        unset($required_attr[$label]);
                    }
                }
            }
        }
        if($required_attr){
            foreach($required_attr as $label => &$field_name){
                $values = AmazonFeedValues::getFieldValues($tpl_id, $site_id, $field_name, $product_type);
                if(empty($values)){
                    $values = AmazonFeedValues::getFieldValues($tpl_id, $site_id, $field_name);
                }
                if(empty($values)){
                    $attrbutes[] = ["ShowType"=>"String","name"=>$label,"required"=>true,"sku"=>false,"values"=>''];
                }else{
                    $attrbutes[] = ["ShowType"=>"List","name"=>$label,"required"=>true,"sku"=>false,"values"=>$values];
                }
            }
        }
        if(count($attrbutes) == 0){
            return json_encode(["status"=>false,"msg"=>"no data"]);
        }
        $response = array("content" => $attrbutes, "success" => true, "specifics" => []);
        return Json::encode($response);
    }



	/*
	* 批量修改价格 price (接口测试ok)  adasdasd
	*  http://localhost/crossborder/web/index.php?r=product/batch-modify-price&ids=1,2&shopId=1312&operationType=replace&price=13.14
	*/
	public function actionBatchModifyPrice(){
        $ids = Yii::$app->request->get('ids');//操作的产品IDs,逗号连接 例如：1,2,3
        $shopId = Yii::$app->request->get('shopId');//操作的产品IDs,逗号连接 例如：1,2,3
        $operationType = Yii::$app->request->get('operationType');//操作类型 替换replace/原有基础加上add/原有基础减去subtract
        $price = Yii::$app->request->get('price');//前端输入的价格（需要对数据做 Integer 校验吗?）
        $idsArr = explode(",",$ids);
        //批量查询出来
        $products = GoodsSoldInfo::find()
            ->where(['is_deleted' => 'N','shop_id' => $shopId])
            ->andWhere(['in','goods_info_id',$idsArr])
            ->all();
        $message = "操作[批量修改价格]成功";
        foreach($products as $product){
            if($operationType=="add"){
                $product->goods_price += $price;
            }else if($operationType == "replace"){
                $product->goods_price = $price;
            }else if($operationType == "subtract"){
                $product->goods_price -= $price;
            }else{
                $message = "操作[批量修改价格] 暂无更新";
            }
            $product->save();
        }
        return SeaShellResult::success($message);
	}

	/*
    * 批量修改库存 qty (接口测试ok) todo 要改
    * http://localhost/crossborder/web/index.php?r=product/batch-modify-qty&ids=1,2&shopId=1312&operationType=replace&qty=12
    */
    public function actionBatchModifyQty(){
        $ids = Yii::$app->request->get('ids');//操作的产品IDs,逗号连接 例如：1,2,3
        $shopId = Yii::$app->request->get('shopId');//当前登录人 操作的店铺ID
        $operationType = Yii::$app->request->get('operationType');//操作类型 替换replace/原有基础加上add/原有基础减去subtract
        $qty = Yii::$app->request->get('qty');//前端输入的库存（需要对数据做 Integer 校验吗?）
        $idsArr = explode(",",$ids);
        //批量查询出来
        $products = GoodsSoldInfo::find()
            ->where(['is_deleted' => 'N','shop_id' => $shopId])
            ->andWhere(['in','goods_info_id',$idsArr])
            ->all();
        $message = "操作[批量修改库存]成功";
        foreach($products as $product){
            if($operationType=="add"){
                $product->qty += $qty;
            }else if($operationType == "replace"){
                $product->qty = $qty;
            }else if($operationType == "subtract"){
                $product->qty -= $qty;
            }else{
                $message = "操作[批量修改库存] 暂无更新";
            }
            $product->save();
        }
        return SeaShellResult::success($message);
    }

    /*
    * 2016-06-06
    * 批量修改促销价格 goods_promotion_price （接口测试ok）
    * http://localhost/crossborder/web/index.php?r=product/batch_modify_promotion_price&ids=1,2&shopId=1312&promotionTimeStart=2016-06-08&promotionTimeEnd=2016-06-09&operationType=replace&price=12.35
    */
    public function actionBatchModifyPromotionPrice(){
        $shopId = Yii::$app->request->get('shopId');//当前登录人 操作的店铺ID
        $ids = Yii::$app->request->get('ids');//操作的产品IDs,逗号连接 例如：1,2,3
        $operationType = Yii::$app->request->get('operationType');//操作类型 替换replace/原有基础加上add/原有基础减去subtract
        $price = Yii::$app->request->get('price');//前端输入的促销价格（需要对数据做 Integer 校验吗?）
        $promotionTimeStart = Yii::$app->request->get('promotionTimeStart');//前端输入的促销开始时间
        $promotionTimeEnd = Yii::$app->request->get('promotionTimeEnd');//前端输入的促销结束时间

        $idsArr = explode(",",$ids);

        $products = GoodsSoldInfo::find()
        ->where(['is_deleted' => 'N','shop_id' => $shopId])
        ->andwhere(['in','goods_info_id',$idsArr])
        ->all();

        $message = "操作[批量修改促销信息]成功";
        foreach($products as $product){
            if($operationType=="add"){
                $products->goods_promotion_price += $price;
            }else if($operationType == "replace"){
                $products->goods_promotion_price = $price;
            }else if($operationType == "subtract"){
                $products->goods_promotion_price -= $price;
            }else{
                $message = "操作[批量修改促销信息] 暂无更新";
            }
            $products->promotion_time_start = $promotionTimeStart;//促销开始时间设置
            $products->promotion_time_end = $promotionTimeEnd;//促销结束时间设置
            $products->save();
        }
        return SeaShellResult::success($message);
    }

    /**
    * 2016-06-14
    * 修改单个SKU的库存
    *
    */
    public function actionEditQtyAndPrice(){
        $shopId = Yii::$app->request->get('shopId');//当前登录人 操作的店铺ID
        $id = Yii::$app->request->get('ids');//操作的产品售卖信息ID（表 yii_goods_sold_info 的自增ID）
        $qty = Yii::$app->request->get('qty');//库存
        $userId = Yii::$app->request->get('userId');//当前操作人
        if(empty($shopId) || empty($id) || empty($userId) ||empty($qty) ){
            $message = "请传入参数 [店铺ID、商品售卖ID、操作人ID、库存]";
            return SeaShellResult::error($message);
        }
        $product = GoodsSoldInfo::find()
            ->where(['is_deleted' => 'N','shop_id' => $shopId,'id' => $id])
            ->one();
        $product->qty=$qty;
        $product->modifier=$userId;
        $product->gmt_modified=date("Y-m-d H:i:s");

        $editQtyResult = $product->update();
        if(!$editQtyResult){
            $message = "更新SKU库存失败";
            return SeaShellResult::error($message);
        }
        return SeaShellResult::success($editQtyResult);
    }



    /**
    * 2016-06-14
    * 修改单个SKU的 价格 促销时间、促销价格
    *
    */
    public function actionEditPromotionInfo(){
        $shopId = Yii::$app->request->get('shopId');//当前登录人 操作的店铺ID
        $id = Yii::$app->request->get('ids');//操作的产品售卖信息ID
        $price = Yii::$app->request->get('price');//前端输入的价格（需要对数据做 Integer 校验吗?）
        $promotionPrice = Yii::$app->request->get('promotionPrice');//前端输入的促销价格（需要对数据做 Integer 校验吗?）
        $promotionTimeStart = Yii::$app->request->get('promotionTimeStart');//前端输入的促销开始时间
        $promotionTimeEnd = Yii::$app->request->get('promotionTimeEnd');//前端输入的促销结束时间
        $userId = Yii::$app->request->get('userId');//当前操作人

        if(empty($shopId) || empty($id) || empty($userId)  ){
            $message = "请传入参数 [店铺ID、商品售卖ID、操作人ID]";
            return SeaShellResult::error($message);
        }
        if(empty($price) && empty($promotionPrice) && empty($promotionTimeStart) && empty($promotionTimeEnd) ){
            $message = "请传入需要更新的参数 [价格、促销价格、促销时间 ]";
            return SeaShellResult::error($message);
        }

        $product = GoodsSoldInfo::find()
            ->where(['is_deleted' => 'N','shop_id' => $shopId,'id' => $id])
            ->one();
        if(!empty($promotionTimeStart)){
            $product->promotion_time_start=$promotionTimeStart;
        }
        if(!empty($promotionTimeEnd)){
            $product->promotion_time_end=$promotionTimeEnd;
        }
        if(!empty($promotionPrice)){
            $product->goods_promotion_price=$promotionPrice;
        }
        if(!empty($price)){
            $product->goods_price=$price;
        }
        $product->modifier=$userId;
        $product->gmt_modified=date("Y-m-d H:i:s");

        $editResult = $product->update();
        if(!$editResult){
            $message = "更新失败";
            return SeaShellResult::error($message);
        }
        return SeaShellResult::success($editResult);
    }

    /**
    * 2016-06-14
    * 查看商品售卖信息（价格、促销价格、促销时间、库存）
    */
    public function actionViewSold(){
        $shopId = Yii::$app->request->get('shopId');//当前登录人 操作的店铺ID
        $id = Yii::$app->request->get('ids');//操作的产品售卖信息ID（yii_goods_sold_info 这个表的自增长ID）
        if(empty($id) || empty($shopId)){
            $message="请传入参数 [ID、店铺ID] ";
            return SeaShellResult::error($message);
        }
        $product = GoodsSoldInfo::find()
            ->where(['is_deleted' => 'N','shop_id' => $shopId,'id' => $id])
            ->one();
        if($product){
            $message = "查无数据";
            return SeaShellResult::error($message);
        }
        return SeaShellResult::arrayToJson($product);
    }

    /**
    * 2016-06-14
    * 修改所有变体基本信息
    */
    public function actionViewBasic(){
         $shopId = Yii::$app->request->get('shopId');//当前登录人 操作的店铺ID
         $id = Yii::$app->request->get('ids');//操作的产品ID(yii_goods_info的自增ID)
         if(empty($shopId) || empty($id) ){
             $message="请传入参数 [ID、店铺ID] ";
             return SeaShellResult::error($message);
         }
         //先查出这个SKU的基本售卖信息
         $product = GoodsInfo::find()
             ->where(['is_deleted' => 'N','shop_id' => $shopId,'id' => $id])
             ->one();
         $data['bullets']=[$product->bullet1,$product->bullet2,$product->bullet3,$product->bullet4,$product->bullet5];
         $data['keyWords']=[$product->key_word1,$product->key_word2,$product->key_word3,$product->key_word4,$product->key_word5];
         $data['description']=$product->description;

         //取出该产品的变体售卖信息
         $productSolds = $product = GoodsSoldInfo::find()
                            ->where(['is_deleted' => 'N','shop_id' => $shopId,'goods_info_id' => $id])
                            ->all();
         foreach($productSolds as $key => $productSold){
            $data['title'][$key]['sku'] = $productSold->sku_sn;
            $data['title'][$key]['title'] = $product->goods_name;
         }

        return SeaShellResult::arrayToJson($data);
    }


    /**
    * 2016-06-14
    * 修改基本信息 #联调换post请求
    */
    public function actionEditBasic(){
        $shopId = Yii::$app->request->get('shopId');//当前登录人 操作的店铺ID
        $id = Yii::$app->request->get('ids');//操作的产品ID
        $keyWords = Yii::$app->request->get('keyWords');//关键词 数组
        $bulletPoints = Yii::$app->request->get('bulletPoints');//数组
        $title = Yii::$app->request->get('title');//数组
        $description = Yii::$app->request->get('description');//描述
        $itemType = Yii::$app->request->get('itemType');//
        $userId = Yii::$app->request->get('userId');//当前操作人

        if(empty($shopId) || empty($id) || empty($userId)){
            $message="请传入参数 [ID、店铺ID、操作人ID] ";
            return SeaShellResult::error($message);
        }
        //先查出这个SKU的基本售卖信息
        $product = GoodsInfo::find()
            ->where(['is_deleted' => 'N','shop_id' => $shopId,'id' => $id])
            ->one();
        if(!$product){
            $message="查无数据 ";
            return SeaShellResult::error($message);
        }
        $product->description = $description;
        $product->itemType = $itemType;
        if(!empty($keyWords) && is_array($keyWords)){
            //强制把数据库中关键词的数据清空 后面准备重新赋值
            $product->key_word="";
            $product->key_word2="";
            $product->key_word3="";
            $product->key_word4="";
            $product->key_word5="";
            foreach($keyWords as $key => $keyWord){
                $words = "key_word";
                if($key>0){
                    $words = "key_word".($key+1);
                }
                $product->$words = $keyWord;
            }
        }
        if(!empty($bulletPoints) && is_array($bulletPoints)){
            //强制把数据库中bullet_point的数据清空 后面准备重新赋值
            $product->bullet_point="";
            $product->bullet_point2="";
            $product->bullet_point3="";
            $product->bullet_point4="";
            $product->bullet_point5="";
            foreach($bulletPoints as $key => $bulletPoint){
                $words = "bullet_point";
                if($key>0){
                    $words = "bullet_point".($key+1);
                }
                $product->$words = $bulletPoint;
            }
        }
        $product->gmt_modified = date("Y-m-d H:i:s");
        $product->modifier = $userId;

        $editBasicResult = $product->update();
        if(!$editBasicResult){
            return SeaShellResult::error("修改失败");
        }
        return SeaShellResult::success($editBasicResult);

    }

    public function actionEcho(){
        echo __METHOD__;
        Yii::trace("trace,开发调试时候记录");
        Yii::error("error,错误日志",__METHOD__);//建议这种
        Yii::warning("warning,警告信息");
        Yii::info("info,记录操作提示",__METHOD__);
        echo "测试 日志";
    }

    //备用接口 手动写入feed表数据
    public function actionSaveFeed(){
        $good_id = Yii::$app->request->get('good_id');
        $this->actionCreateTplData($good_id);
        echo "add feed ok";
    }


}


?>