<?php
  use yii\widgets\LinkPager;

  $shopId = Yii::$app->request->get('shopId');
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title >商品跟卖_详情</title>
<link href="/css/bootstrap.min.css" rel="stylesheet" />
<link href="/css/common/style.css" rel="stylesheet" />
<link href="/css/layout/layout.css" rel="stylesheet" />
<link href="/css/common/alert.css" rel="stylesheet" />
<link href="/css/common/frame.css" rel="stylesheet" />
<link rel="stylesheet" href="/css/shop/product.css" />
<link rel="stylesheet" href="/css/shop/create.css" />
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
<div id="siderbarNav3"></div>
<div class="container-fluid">
  <div class="row">
    <div class="wrap">
      <div class="col-md-12">
        <div class="workspace">
          <ul class="breadcrumb">
            <li> <i class="glyphicon glyphicon-home"></i> <a href="javascript:void(0)">店铺</a> </li>
            <li><a href="javascript:void(0)">商品跟卖 详情</a></li>
          </ul>
          <div class="box green">
            <div class="box-header">
              <h2> <i class="glyphicon glyphicon-list"></i> <span class="break"></span> 商品跟卖 详情 </h2>
            </div>
            <div class="box-content">
              <ul class="nav nav-tabs">
                <li role="presentation" class="<?= $status == 'current' ? 'active' : '' ?>"> 
                  <a href="?r=monitor/detail&shopId=<?= $shopId ?>&status=current&id=<?= $monitorId ?>"> 当前卖家列表 
                    <span class="badge">
                      <?= (int)$count['new'] ?>
                    </span> 
                  </a> 
                </li>

                <li role="presentation" class="<?= $status == 'old' ? 'active' : '' ?>"> 
                  <a href="?r=monitor/detail&shopId=<?= $shopId ?>&status=old&id=<?= $monitorId ?>"> 历史卖家列表 
                    <span class="badge">
                      <?= (int)$count['old'] ?>
                    </span> 
                  </a> 
                </li>
              </ul>
              
              <div class="operation-bar">
                
              </div>
              <table class="table">
                <tbody>
                  <tr>
                    <th class="pro-5" style="min-width: 50px"> <div class="btn-group"></th>
                    <th class="pro-15">卖家名称</th>
                    <th class="pro-10">卖家ID</th>
                    <th class="pro-10 orderby-price" data-id="price" data-value="<?= $sort;?>">单价</th>
                    <th class="pro-5 orderby-fee" data-id="shopping_fee" data-value="<?= $sort;?>" >邮费</th>
                    <th class="pro-5">总价</th>
                    <th class="pro-5">FBA</th>
                    <th class="pro-15 orderby-follow" data-id="follow_sell_at" data-value="<?= $sort;?>" >跟卖时间</th>
                    <?php if ($status == 'current') { ?>
                      <th class="pro-15 orderby-monitor" data-id="last_monitor_at" data-value="<?= $sort;?>" >监控时间</th>
                    <?php } else { ?>
                      <th class="pro-15 orderby-follow-end" data-id="follow_sell_end_at" data-value="<?= $sort; ?>" >跟卖结束时间</th>
                    <?php } ?>

                  </tr>
                <?php if ($detail) { ?>
                     <?php foreach ($detail as $one) { ?>
                    <tr>
                      <td></td>
                      <td><?= $one['seller_name'] ?></td>
                      <td><?= $one['seller_id'] ?></td>
                      <td>$<?= $one['price'] ?></td>
                      <td>$<?= $one['shopping_fee'] ?></td>
                      <td>$<?= sprintf('%0.2f', $one['price'] + $one['shopping_fee']) ?></td>
                      <td><?= $one['isFBA'] ? '是' : '否' ?></td>
                      <td><?= date('Y-m-d H:i:s', $one['follow_sell_at']) ?></td>
                      <td>
                        
                        <?php if ($status == 'current') { ?>
                          <?= $one['last_monitor_at'] ? date('Y-m-d H:i:s', $one['last_monitor_at']) : '暂无' ?>
                        <?php } else { ?>
                          <?= date('Y-m-d H:i:s', $one['follow_sell_end_at']) ?>
                        <?php } ?>
                      </td>
                    </tr>
                  <?php } ?>
                <?php } else { ?>
                    <tr><td colspan="9">没有找到记录</td></tr>
                <?php } ?>
                 

                </tbody>
              </table>
              <div class="row footer-stat">
                <ul class="nav pull-right">
                  <li>
                    <?= $pageString ?>
                  </li>
                  <li class="btn-group page-n-ctrl">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false"> 每页显示<?= isset($_GET['page_no']) ? intval($_GET['page_no']) : 30 ?>个 <span class="caret"></span> </button>
                    <ul class="dropdown-menu" role="menu">
                      <li><a href="<?= $requestUri . '&page_no=30' ?>">30</a></li>
                      <li><a href="<?= $requestUri . '&page_no=50' ?>">50</a></li>
                      <li><a href="<?= $requestUri . '&page_no=100' ?>">100</a></li>
                      <li><a href="<?= $requestUri . '&page_no=200' ?>">200</a></li>
                    </ul>
                  </li>
                  <?php if ($totalCount > 0) {?>
                    <li>共<?= $totalCount ?>页</li>

                  <?php } else { ?>
                      <li>无记录</li>
                  <?php } ?>
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
<script src="/js/create/template.js"></script> 
<script src="/js/monitor/followseller.js"></script> 
<script src="/js/moment-with-locales.js"></script> 
<script src="/js/bootstrap-datetimepicker.js"></script>
</body>
</html>