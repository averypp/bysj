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
              <h2> <i class="glyphicon glyphicon-list"></i> <span class="break"></span> 调价记录 </h2>
            </div>
            <div class="box-content">
              <ul class="nav nav-tabs">
                <li role="presentation" class="<?= $href == 'list' ? 'active' : '' ?>"> <a href="?r=bidding&shopId=<?= $shopId ?>"> 调价商品 </a> </li>
                <li role="presentation" class="<?= $href == 'rulelist' ? 'active' : '' ?>"> <a href="?r=bidding/rulelist&shopId=<?= $shopId ?>"> 调价规则 </a> </li>
                <li role="presentation" class="<?= $href == 'log' ? 'active' : '' ?>"> <a href="?r=bidding/log&shopId=<?= $shopId ?>"> 调价记录 </a> </li>
              </ul>
              <div class="operation-bar">
                <div>

                  <div class="" style="max-width: 500px;margin-top: 5px;">
                    <div class="input-group">
                      <input type="text" class="form-control" id="asin-input" placeholder="(请填写需要查询的ASIN)" value="<?= $search_asin ?>"/>
                      <span class="input-group-btn">
                        <button class="btn btn-success" type="button" id="asin-search-btn">查询</button>
                      </span>
                    </div>
                  </div>

                </div>
              </div>
              <table class="table">
                <thead>
                  <tr>
                    <th>ASIN</th>
                    <th>sku</th>
                    <th>最小价格</th>
                    <th>最大价格</th>
                    <th>规则名称</th>
                    <th>调价之前</th>
                    <th>调价之后</th>
                    <th>价格变动</th>
                    <th>调价日期</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(count($logs)){?>
                    <?php foreach($logs as $log){ //var_dump($log);?>
                      <tr>
                        <td><?php echo $log['asin'];?></td>
                        <td><?php echo $log['sku'];?></td>
                        <td><?php echo $log['mix_price'];?></td>
                        <td><?php echo $log['max_price'];?></td>
                        <td><?php echo $log['rules_name']?></td>
                        <td><?php echo $log['before_price']?></td>
                        <td><?php echo $log['after_price']?></td>
                        <td><?php echo $log['change_price']?></td>
                        <td><?php echo $log['date']?></td>
                      </tr>
                    <?php }?>
                  <?php }else{?>
                    <tr> <td colspan="16">暂无记录</td> </tr>
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
<script src="/js/moment-with-locales.js"></script> 
<script src="/js/bootstrap-datetimepicker.js"></script>
<script src="/js/bidding/bidding.js"></script>
</body>
</html>