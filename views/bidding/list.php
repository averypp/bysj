<?php
  use yii\widgets\LinkPager;

  $shopId = Yii::$app->request->get('shopId');
  $links = $pages->getLinks();
  $prevLink = isset($links['prev']) ? $links['prev'] : '';
  $nextLink = isset($links['next']) ? $links['next'] : '';

?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title >智能调价</title>
  <link href="/css/bootstrap.min.css" rel="stylesheet" />
  <link href="/css/common/style.css" rel="stylesheet" />
  <link href="/css/layout/layout.css" rel="stylesheet" />
  <link href="/css/common/alert.css" rel="stylesheet" />
  <link href="/css/common/frame.css" rel="stylesheet" />
  <link rel="stylesheet" href="/css/shop/product.css" />
  <link rel="stylesheet" href="/css/shop/create.css" />
  <link rel="stylesheet" href="css/shop/online.css" /> 
  <link rel="stylesheet" href="css/shop/group.css" /> 
  <link rel="stylesheet" href="/css/bootstrap-datetimepicker.css" />
  <!--[if lt IE 9]>
    <script src="js/html5shiv.min.js"></script>
    <script src="js/respond.min.js"></script>
  <![endif]-->
</head>
<body>
<div class="md-modal md-effect-1" id="global-inform">
  <div class="md-content">
    <h3 class="md-header">系统通知</h3>
    <div class="md-body">
      <div class="md-loading">
        <div class="md-loading-icon"></div>
        <div class="md-loading-text"></div>
      </div>
      <div class="md-panel"></div>
    </div>
    <div class="md-footer">
      <button class="btn btn-primary close-inform">关闭</button>
    </div>
  </div>
</div>
<div class="md-overlay"></div>
<input type="hidden" value="<?= $shopId ?>" id="shopId" />
<input type="hidden" value="<?= $shopInfo['platformName'] ?>" id="platformName" />
<input type="hidden" value="<?= $shopInfo['siteName'] ?>" id="siteName" />
<input type="hidden" value="<?= $shopInfo['name'] ?>" id="shopName" />
<input type="hidden" value="<?= $BRcount ?>" id="BRcount">
<!-- /.modal -->
<div class="to-top" hidden="hidden"></div>
<div id="headNav"></div>
<div id="siderbarNav5"></div>
<div class="container-fluid">
  <div class="row">
    <div class="wrap">
      <div class="col-md-12">
        <div class="workspace">
          <ul class="breadcrumb">
            <li> <i class="glyphicon glyphicon-home"></i> <a href="javascript:void(0)">店铺</a> </li>
            <li><a href="javascript:void(0)">智能调价</a></li>
          </ul>
          <div class="box green">
            <div class="box-header">
              <h2> <i class="glyphicon glyphicon-list"></i> <span class="break"></span> 调价商品 </h2>
            </div>
            <div class="box-content">
              <ul class="nav nav-tabs">
                <li role="presentation" class="<?= $href == 'list' ? 'active' : '' ?>"> <a href="?r=bidding&shopId=<?= $shopId ?>"> 调价商品 </a> </li>
                <li role="presentation" class="<?= $href == 'rulelist' ? 'active' : '' ?>"> <a href="?r=bidding/rulelist&shopId=<?= $shopId ?>"> 调价规则 </a> </li>
                <li role="presentation" class="<?= $href == 'log' ? 'active' : '' ?>"> <a href="?r=bidding/log&shopId=<?= $shopId ?>"> 调价记录 </a> </li>
              </ul>
              <div class="operation-bar">
                
                <div class="row">
                <?php if ($href == 'list') { ?> 

                  <ul class="condition-box-bd"> 
                    <li class="search-group">
                      <label class="search-label">显示商品：</label>
                      <a href="<?= $searchUri ?>&filter=0" class="search-title <?= $filter == '0' ? 'on' : '' ?>">全部</a>
                      <a href="<?= $searchUri ?>&filter=1" class="search-title <?= $filter == '1' ? 'on' : '' ?>">启动智能调价</a>
                      <a href="<?= $searchUri ?>&filter=2" class="search-title <?= $filter == '2' ? 'on' : '' ?>">有竞争对手</a>
                      <a href="<?= $searchUri ?>&filter=3" class="search-title <?= $filter == '3' ? 'on' : '' ?>">无竞争对手</a>
                      <a href="<?= $searchUri ?>&filter=4" class="search-title <?= $filter == '4' ? 'on' : '' ?>">等于最小价格</a>
                      <a href="<?= $searchUri ?>&filter=5" class="search-title <?= $filter == '5' ? 'on' : '' ?>">等于最大价格</a>
                      <a href="<?= $searchUri ?>&filter=6" class="search-title <?= $filter == '6' ? 'on' : '' ?>">缺少规则</a>
                      <a href="<?= $searchUri ?>&filter=7" class="search-title <?= $filter == '7' ? 'on' : '' ?>">竞争对手低于最小价格</a>
                    </li> 
                    <li class="search-group form-group form-inline"> <label class="search-label">商品搜索：</label> 
                     <div class="input-group"> 
                      <div class="input-group-btn"> 
                       <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="search-key">标题</span> <span class="caret"></span> </button> 
                       <ul class="dropdown-menu" id="search-options"> 
                        <li><a href="javascript: void(0)" data-key="s-title">标题</a></li> 
                        <li><a href="javascript: void(0)" data-key="s-sku">Seller-SKU</a></li> 
                        <li><a href="javascript: void(0)" data-key="s-asin">ASIN</a></li> 
                       </ul> 
                      </div> 
                      <input type="text" class="form-control search-input" name="search" placeholder="标题、Seller-SKU、ASIN" value=""/> 
                     </div> <a href="javascript:void(0)" class="btn btn-success" id="search-btn">搜索</a> </li> 
                   </ul> 
                <?php } ?>
                  <div>
                  已选中 <span id="product-count">0</span>件商品


                    <form id="batch-form" action="#" method="post">
                      <input type="hidden" id="batch-condition" name="condition" />
                      <div class="btn-group" style="margin: 0 0 0 8px;">
                        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 批量操作 <span class="caret"></span> </button>
                        <ul class="dropdown-menu">
                          <li> <a href="javascript: void(0)" id="all-open"> 启动智能调价 </a> </li>
                          <li> <a href="javascript: void(0)" id="all-close"> 暂停智能调价 </a> </li>
                          <li> <a href="javascript: void(0)" id="all-del-rule"> 清空调价规则 </a> </li>
                          <li> <a href="javascript: void(0)" id="all-delete"> 删除调价商品 </a> </li>
                        </ul>
                      </div>
                    </form>

                  </div>
                </div>
              </div>
              <table class="table">
                <thead>
                  <tr>
                    <th> 
                      <div class="btn-group">
                        <div class="btn btn-default all-select"> 全选 </div>
                      </div>
                    </th>
                    <th>状态</th>
                    <th>SKU</th>
                    <th>ASIN</th>
                    <th class="pro-20">商品标题</th>
                    <th>创建日期</th>
                    <th>商品成本</th>
                    <th>最小价格</th>
                    <th>最大价格</th>
                    <th>调价规则</th>
                    <th>竞争对手数</th>
                    <th>您的价格<br>+运费</th>
                    <th>最低价格<br>+运费</th>
                    <th>黄金购物车价格<br>+运费</th>
                    <th>操作</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(count($goods)){ // var_dump($rules);?>
                    <?php foreach($goods as $good){ // var_dump($good);?>
                      <tr>
                        <td><input type="checkbox" class="sel-pro" data-feed-id="<?= $good['id'] ?>" /></td>
                        <td><?php echo $good['status']?'启动':'暂停';?></td>
                        <td><a  class = 'sku-<?= $good['id'] ?>' href="/?r=product-online&shopId=<?= $shopId ?>&k=<?= $good['sku'] ?>"><?php echo $good['sku'];?></a></td>
                        <td><a href="/?r=product-online&shopId=<?= $shopId ?>&p=<?= $good['asin'] ?>"><?php echo $good['asin'];?></a></td>
                        <td><?php echo $good['title'];?></td>
                        <td><?php echo date('Y-m-d h:i:s',$good['create_at']);?></td>
                        <td class = 'cost-<?= $good['id'] ?>'><?php echo $good['cost'];?></td>
                        <td class = 'mix_price-<?= $good['id'] ?>'><?php echo $good['mix_price'];?></td>
                        <td class = 'max_price-<?= $good['id'] ?>'><?php echo $good['max_price'];?></td>
                        <td >
                          <?php if(!$good['rules_id']){ echo "--";}else{?>
                            <?php if(count($rules)){ ?>
                              <?php foreach($rules as $rule){
                                if($rule['id'] == $good['rules_id']){
                                  echo "<a href='?r=bidding/rulelist&shopId={$shopId}&id={$good['rules_id']}'>{$rule['name']}</a>";
                                }
                              }?>
                            <?php }else{echo "--";}?>
                          <?php }?>
                          <input type="hidden" class= "rules_id-<?= $good['id'] ?>" value="<?= $good['rules_id']?>">
                        </td>
                        <td><?php echo $good['competitors_count'];?></td>
                        <td>
                          <?php echo $good['my_price']."<br>+ ".$good['my_price_fare'];?>
                          <input type="hidden" class="my_price-<?= $good['id'] ?>" value="<?php echo $good['my_price']+$good['my_price_fare'];?>">
                        </td>
                        <td><?php echo $good['lower_price']."<br>+ ".$good['lower_price_far'];?></td>
                        <td><?php echo $good['buybox_price']."<br>+ ".$good['buybox_price_fare'];?></td>
                        <td>
                          <div class="btn-group"> 
                           <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="javascript:void(0)"> 操作<span class="caret"></span> </a> 
                           <ul class="dropdown-menu pull-right single-edit-menu" data-id="<?= $good['id'] ?>"> 
                            <li> <a class="single-set"  data-toggle="modal" data-target="#set-modal"> 设置 </a> </li>
                            <?php if($good['status'] == 0){?>
                            <li> <a class="single-close" style="cursor:pointer" data-status = <?php echo $good['status'] ?> > 启动智能调价 </a> </li>
                            <?php }else{?>
                            <li> <a class="single-close" style="cursor:pointer" data-status = <?php echo $good['status']?> > 暂停智能调价 </a> </li>
                            <?php }?>
                            <li> <a class="single-delete" style="cursor:pointer" data-toggle="modal"> 删除调价商品 </a> </li>
                           </ul> 
                          </div> 
                        </td>
                      </tr>
                    <?php }?>
                  <?php }else{?>
                    <tr> <td colspan="16">暂无商品</td> </tr>
                  <?php }?>
                </tbody>
              </table>
              <div class="row footer-stat">
                <ul class="nav pull-right">
                  <li>
                    <?php 
                        echo LinkPager::widget([
                            'pagination' => $pages,
                            'options' => [
                                'class' => 'pagination page-bar',
                            ],
                            'hideOnSinglePage' => false,
                        ]);
                    ?>
                  </li>
                  <li class="btn-group page-n-ctrl">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false"> 每页显示<?= isset($_GET['page_no']) ? intval($_GET['page_no']) : 10 ?>个 <span class="caret"></span> </button>
                    <ul class="dropdown-menu" role="menu">
                      <li><a href="<?= $requestUri . '&page_no=10' ?>">10</a></li>
                      <li><a href="<?= $requestUri . '&page_no=50' ?>">50</a></li>
                      <li><a href="<?= $requestUri . '&page_no=100' ?>">100</a></li>
                    </ul>
                  </li>
                  <li>共<?= $pages->getPageCount() ?>页</li>
                </ul>
              </div>
              <div id="set-cate-con"> </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- edit stock and price Modal --> 
  <div class="modal" id="set-modal" tabindex="-1" role="dialog" aria-labelledby="claimModalLabel" aria-hidden="true"> 
   <div class="modal-dialog"> 
    <div class="modal-content"> 
     <div class="modal-header"> 
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button> 
      <h4 class="modal-title"> 设置商品调价 </h4> 
     </div> 
     <div class="modal-body limit-height-modal"> 
      <div class="form-horizontal"> 
       <div class="form-group"> 
        <label for="" class="col-md-3 control-label"> SKU: </label> 
        <div class="col-md-8"> 
         <input type="text" class="form-control" id="var-sku" required="" value="" readonly="" /> 
         <input id="bidding_id" hidden="" value="" /> 
         <input id="my_price" hidden="" value="" />
        </div> 
       </div> 
       
       <hr noshade="noshade" /> 
       <div class="form-group"> 
        <label for="" class="col-md-3 control-label"> <span class="required">*</span> 成本价: </label> 
        <div class="col-md-8"> 
         <input type="number" min="0" step="0.01" class="form-control" id="var-cost" required="" value="" /> 
        </div> 
       </div> 
       <div class="form-group"> 
        <label for="" class="col-md-3 control-label"> <span class="required">*</span> 最小价格: </label> 
        <div class="col-md-8"> 
         <input type="number" min="0" step="0.01" class="form-control" id="var-mix" required="" value="" /> 
        </div> 
       </div>
       <div class="form-group"> 
        <label for="" class="col-md-3 control-label"> <span class="required">*</span> 最大价格: </label> 
        <div class="col-md-8"> 
         <input type="number" min="0" step="0.01" class="form-control" id="var-max" required="" value="" /> 
        </div> 
       </div>
       

       <hr noshade="noshade" /> 
       <div class="form-group"> 
        <label for="" class="col-md-3 control-label"> 调价规则: </label> 
        <div class="col-md-8"> 
          <select class="form-control" id="var-rule">
            <?php if( count($rules) ){?>
              <?php foreach($rules as $rule){ ?>
                <option value="<?= $rule['id']?>"> <?= $rule['name']?> </option>
              <?php }?>
            <?php }?>
          </select>
        </div> 
       </div> 

       <div class="form-group"> 
        <div class="col-md-11"> 
         <button type="button" class="btn btn-info var-update price" data-name="edit-vars" style="float: right">设置</button> 
        </div> 
       </div> 
      </div> 
     </div> 
    </div> 
   </div> 
  </div> 
  <!-- /.modal --> 

<!-- 模态框（Modal） -->
<div class="modal fade" id="delConfirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width: 30%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
        <h4 class="modal-title" id="myModalLabel"> 提示 </h4>
      </div>
      <div class="modal-body">
        <div class="md-loading">
          <div class="md-loading-text" style="font-size:16px;">确认删除调价商品吗？</div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="button" class="btn btn-primary" data-loading-text="处理中..." data-id="" id="del-monitor" autocomplete="off">确认</button>
      </div>
    </div>
    <!-- /.modal-content --> 
  </div>
</div>
<!-- /.modal -->

<!-- 模态框（Modal） --> 
  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"> 
    <div class="modal-dialog"> 
      <div class="modal-content"> 
        <div class="modal-header"> 
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button> 
          <h4 class="modal-title" id="myModalLabel"> 切换店铺 </h4> 
        </div> 
        <div class="modal-body"> 
          <div class="row" id="shops"></div> 
        </div> 
        <div class="modal-footer"> 
          <button type="button" class="btn btn-default" data-dismiss="modal">关闭 </button> 
        </div> 
      </div>
      <!-- /.modal-content --> 
    </div> 
  </div>
<!-- /.modal --> 

<div id="footerNav"></div>
<script src="/js/hm.js"></script> 
<script src="/js/jquery.min.js"></script> 
<script src="/js/public.js"></script> 
<script src="/js/bootstrap.min.js"></script> 
<script src="/js/common/logout.js"></script> 
<script src="/js/common/inform.js"></script>
<script src="/js/shop/shop.js"></script> 
<script src="/js/util/checkbox.js"></script>
<script src="/js/bidding/bidding.js"></script>
<script src="/js/moment-with-locales.js"></script> 
<script src="/js/bootstrap-datetimepicker.js"></script>
</body>
</html>