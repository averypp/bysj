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
              <h2> <i class="glyphicon glyphicon-list"></i> <span class="break"></span> 调价规则 </h2>
            </div>
            <div class="box-content">
              <ul class="nav nav-tabs">
                <li role="presentation" class="<?= $href == 'list' ? 'active' : '' ?>"> <a href="?r=bidding&shopId=<?= $shopId ?>"> 调价商品 </a> </li>
                <li role="presentation" class="<?= $href == 'rulelist' ? 'active' : '' ?>"> <a href="?r=bidding/rulelist&shopId=<?= $shopId ?>"> 调价规则 </a> </li>
                <li role="presentation" class="<?= $href == 'log' ? 'active' : '' ?>"> <a href="?r=bidding/log&shopId=<?= $shopId ?>"> 调价记录 </a> </li>
              </ul>
              <div class="operation-bar">
                <div class="row">
                  <a class="btn btn-primary" role="button" href="?r=bidding/edit-rule&shopId=<?= $shopId ?>"><i class="glyphicon glyphicon-plus"> </i> 新增规则</a>
                </div>
              </div>
              <table class="table">
                <thead>
                  <tr>
                    <!-- <th>编号</th> -->
                    <th>规则名称</th>
                    <th class="pro-40">规则说明</th>
                    <th>应用商品数</th>
                    <th>操作</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(count($rules)){?>
                    <?php foreach($rules as $rule){ //var_dump($rule);?>
                      <tr>
                      <?php if(0): ?>
                        <td><?php echo $rule['id'];?></td>
                      <?php endif; ?>
                        <td><a href="/?r=bidding/edit-rule&shopId=<?= $shopId ?>&rid=<?= $rule['id'] ?>"><?php echo $rule['name'];?></a></td>
                        <td><?php echo $rule['description'];?></td>
                        <td>
                          <?php if($rule['seller_count']){?>
                          <a href="/?r=bidding&shopId=<?= $shopId ?>&rid=<?php echo $rule['id'];?>">
                            <?php echo $rule['seller_count'];?>
                          </a>
                          <?php }else{ echo $rule['seller_count']; }?>
                        </td>
                        <td>
                          <div class="btn-group"> 
                           <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="javascript:void(0)"> 操作<span class="caret"></span> </a> 
                           <ul class="dropdown-menu pull-right single-edit-menu" data-id="<?= $rule['id'] ?>"> 
                            <li> <a class="rulelist-single-set" href="javascript:void(0)"> 编辑规则 </a> </li>
                            <li> <a class="rulelist-single-delete" href="javascript:void(0)" data-toggle="modal" data-target=""> 删除规则 </a> </li>
                           </ul> 
                          </div> 
                        </td>
                      </tr>
                    <?php }?>
                  <?php }else{?>
                    <tr> <td colspan="16">暂无规则</td> </tr>
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
        
          <div class="md-loading-text" style="font-size:16px;">确认删除该规则吗？</div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="button" class="btn btn-primary" data-loading-text="处理中..." data-id="" id="rulelist-del-monitor" autocomplete="off">确认</button>
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